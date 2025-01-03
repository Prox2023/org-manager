<?php

namespace OrgManager\Features\Discord;

use OrgManager\Features\Feature;
use OrgManager\Features\Interfaces\HasSettings;
use OrgManager\Features\Interfaces\HasAdminPage;

class DiscordAuthFeature extends Feature implements HasSettings, HasAdminPage {
    protected string $id = 'discord-auth';
    protected string $name = 'Discord Authentication';
    protected string $description = 'Enables Discord authentication and organization management';
    protected array $tags = ['authentication', 'discord', 'organization'];
    protected bool $enabled = true;

    private const OPTION_GROUP = 'org_manager_discord';
    private const OPTION_NAME = 'org_manager_discord_settings';

    public function initialize(): void {
        if (WP_DEBUG) {
            error_log('Initializing DiscordAuthFeature');
        }
        add_action('init', [$this, 'init_discord_client']);
        add_action('wp_login', [$this, 'sync_discord_roles'], 10, 2);
        add_filter('authenticate', [$this, 'check_discord_organization'], 30, 3);
        $this->register_rest_routes();
    }

    public function register_hooks(): void {
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function register_settings(): void {
        register_setting(
            self::OPTION_GROUP,
            self::OPTION_NAME,
            [
                'type' => 'object',
                'description' => 'Discord authentication settings',
                'sanitize_callback' => [$this, 'sanitize_settings'],
                'default' => [
                    'client_id' => '',
                    'client_secret' => '',
                    'redirect_uri' => '',
                    'allowed_roles' => [],
                    'registration_enabled' => false,
                ]
            ]
        );

        add_settings_section(
            'discord_main_section',
            'Discord Configuration',
            [$this, 'render_settings_section'],
            'org-manager-discord'
        );

        add_settings_field(
            'discord_client_id',
            'Client ID',
            [$this, 'render_text_field'],
            'org-manager-discord',
            'discord_main_section',
            ['label_for' => 'discord_client_id', 'field' => 'client_id']
        );

        add_settings_field(
            'discord_client_secret',
            'Client Secret',
            [$this, 'render_text_field'],
            'org-manager-discord',
            'discord_main_section',
            ['label_for' => 'discord_client_secret', 'field' => 'client_secret']
        );

        add_settings_field(
            'discord_redirect_uri',
            'Redirect URI',
            [$this, 'render_text_field'],
            'org-manager-discord',
            'discord_main_section',
            [
                'label_for' => 'discord_redirect_uri',
                'field' => 'redirect_uri',
                'description' => 'The URL where users will be redirected after Discord authentication (e.g., https://your-site.com/wp-json/org-manager/v1/discord/auth)'
            ]
        );

        add_settings_field(
            'discord_registration_enabled',
            'Enable Registration',
            [$this, 'render_checkbox_field'],
            'org-manager-discord',
            'discord_main_section',
            [
                'label_for' => 'discord_registration_enabled',
                'field' => 'registration_enabled',
                'description' => 'Allow new users to register through Discord'
            ]
        );
    }

    public function register_rest_routes(): void {
        if (WP_DEBUG) {
            error_log('Registering REST routes for Discord');
        }

        add_action('rest_api_init', function() {
            // Debug endpoint to list all routes
            register_rest_route('org-manager/v1', '/routes', [
                'methods' => 'GET',
                'callback' => function() {
                    global $wp_rest_server;
                    error_log('Available routes: ' . print_r($wp_rest_server->get_routes(), true));
                    return new \WP_REST_Response($wp_rest_server->get_routes(), 200);
                },
                'permission_callback' => [$this, 'check_admin_permission']
            ]);

            // Get settings endpoint
            register_rest_route('org-manager/v1', '/discord/settings', [
                'methods' => ['GET', 'POST'],
                'callback' => [$this, 'handle_settings_request'],
                'permission_callback' => [$this, 'check_admin_permission'],
                'args' => [
                    'client_id' => [
                        'required' => false,
                        'type' => 'string',
                        'sanitize_callback' => 'sanitize_text_field'
                    ],
                    'client_secret' => [
                        'required' => false,
                        'type' => 'string',
                        'sanitize_callback' => 'sanitize_text_field'
                    ],
                    'redirect_uri' => [
                        'required' => false,
                        'type' => 'string',
                        'sanitize_callback' => 'sanitize_text_field'
                    ],
                    'registration_enabled' => [
                        'required' => false,
                        'type' => 'boolean',
                        'default' => false
                    ]
                ]
            ]);

            register_rest_route('org-manager/v1', '/discord/auth', [
                'methods' => 'GET',
                'callback' => [$this, 'handle_auth_callback'],
                'permission_callback' => '__return_true'
            ]);
        });
    }

    public function register_admin_page(): void {
        add_submenu_page(
            'org-manager-features',
            'Discord Settings',
            'Discord Settings',
            $this->get_capability(),
            'org-manager-discord',
            [$this, 'render_admin_page']
        );
    }

    public function render_admin_page(): void {
        if (!current_user_can($this->get_capability())) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields(self::OPTION_GROUP);
                do_settings_sections('org-manager-discord');
                submit_button('Save Settings');
                ?>
            </form>
        </div>
        <?php
    }

    public function get_settings(): array {
        return get_option(self::OPTION_NAME, []);
    }

    public function update_settings(array $settings): bool {
        return update_option(self::OPTION_NAME, $settings);
    }

    public function render_settings_section(): void {
        echo '<p>Configure your Discord integration settings here.</p>';
    }

    public function render_text_field(array $args): void {
        $settings = $this->get_settings();
        $field = $args['field'];
        $value = $settings[$field] ?? '';
        $type = $args['type'] ?? 'text';
        ?>
        <input
            type="<?php echo esc_attr($type); ?>"
            id="<?php echo esc_attr($args['label_for']); ?>"
            name="<?php echo esc_attr(self::OPTION_NAME . "[{$field}]"); ?>"
            value="<?php echo esc_attr($value); ?>"
            class="regular-text"
        />
        <?php
        if (isset($args['description'])) {
            echo '<p class="description">' . esc_html($args['description']) . '</p>';
        }
    }

    public function render_checkbox_field(array $args): void {
        $settings = $this->get_settings();
        $field = $args['field'];
        $checked = isset( $settings[ $field ] ) && (bool) $settings[ $field ];
        ?>
        <input
            type="checkbox"
            id="<?php echo esc_attr($args['label_for']); ?>"
            name="<?php echo esc_attr(self::OPTION_NAME . "[{$field}]"); ?>"
            <?php checked($checked); ?>
            value="1"
        />
        <?php
        if (isset($args['description'])) {
            echo '<p class="description">' . esc_html($args['description']) . '</p>';
        }
    }

    public function sanitize_settings($input): array {
        $sanitized = [];
        
        $sanitized['client_id'] = sanitize_text_field($input['client_id'] ?? '');
        $sanitized['client_secret'] = sanitize_text_field($input['client_secret'] ?? '');
        $sanitized['redirect_uri'] = sanitize_text_field($input['redirect_uri'] ?? '');
        $sanitized['registration_enabled'] = isset($input['registration_enabled']);
        
        return $sanitized;
    }

    public function get_menu_position(): int {
        return 10;
    }

    public function get_capability(): string {
        return 'manage_options';
    }

    public function check_admin_permission(): bool {
        return current_user_can('manage_options');
    }

    public function init_discord_client(): void {
        // Initialize Discord client with settings
        $settings = $this->get_settings();
        if (empty($settings['client_id']) || empty($settings['client_secret'])) {
            return;
        }

        // Initialize Discord API client here
    }

    public function check_discord_organization($user, $username, $password): \WP_User|\WP_Error {
        // If it's not a Discord login attempt, pass through
        if (!isset($_GET['discord_auth'])) {
            return $user;
        }

        $settings = $this->get_settings();
        
        // Check if registration is enabled
        if (!$settings['registration_enabled']) {
            return new \WP_Error(
                'registration_disabled',
                'Discord registration is currently disabled.'
            );
        }

        // Verify user is in the correct guild/organization
        // This would use the Discord API to check membership
        
        return $user;
    }

    /**
     * Handle GET request for settings
     */
    public function handle_get_settings(\WP_REST_Request $request): \WP_REST_Response {
        if (WP_DEBUG) {
            error_log('Handling GET settings request');
        }
        return new \WP_REST_Response($this->get_settings(), 200);
    }

    /**
     * Handle POST request for updating settings
     */
    public function handle_update_settings(\WP_REST_Request $request): \WP_REST_Response {
        if (WP_DEBUG) {
            error_log('Handling POST settings request');
            error_log('Request params: ' . print_r($request->get_params(), true));
        }
        $settings = [
            'client_id' => $request->get_param('client_id'),
            'client_secret' => $request->get_param('client_secret'),
            'redirect_uri' => $request->get_param('redirect_uri'),
            'registration_enabled' => (bool) $request->get_param('registration_enabled'),
            'allowed_roles' => []
        ];

        $updated = $this->update_settings($settings);

        if (!$updated) {
            return new \WP_REST_Response([
                'message' => 'Failed to update settings'
            ], 500);
        }

        return new \WP_REST_Response([
            'message' => 'Settings updated successfully',
            'settings' => $settings
        ], 200);
    }

    /**
     * Handle both GET and POST requests for settings
     */
    public function handle_settings_request(\WP_REST_Request $request): \WP_REST_Response {
        if ($request->get_method() === 'POST') {
            return $this->handle_update_settings($request);
        }
        return $this->handle_get_settings($request);
    }

    /**
     * Synchronize WordPress user roles with Discord roles
     * 
     * @param string $user_login The user's login name
     * @param \WP_User $user WP_User object
     * @return void
     */
    public function sync_discord_roles(string $user_login, \WP_User $user): void {
        if (WP_DEBUG) {
            error_log('Syncing Discord roles for user: ' . $user_login);
        }

        $settings = $this->get_settings();
        if (empty($settings['client_id']) || empty($settings['client_secret'])) {
            if (WP_DEBUG) {
                error_log('Discord settings not configured');
            }
            return;
        }

        // Get Discord user ID from user meta
        $discord_user_id = get_user_meta($user->ID, 'discord_user_id', true);
        if (empty($discord_user_id)) {
            if (WP_DEBUG) {
                error_log('No Discord ID found for user: ' . $user_login);
            }
            return;
        }

        try {
            // Initialize Discord client and sync roles
            $discord_client = new DiscordClient(
                $settings['client_id'],
                $settings['client_secret'],
                ''  // We don't need guild_id for this
            );

            // Get user's Discord roles
            $discord_roles = $discord_client->get_user_roles($discord_user_id);
            if (empty($discord_roles)) {
                if (WP_DEBUG) {
                    error_log('No Discord roles found for user: ' . $user_login);
                }
                return;
            }

            // Update user meta with Discord roles
            update_user_meta($user->ID, 'discord_roles', $discord_roles);

            if (WP_DEBUG) {
                error_log('Successfully synced Discord roles for user: ' . $user_login);
            }
        } catch (\Exception $e) {
            if (WP_DEBUG) {
                error_log('Error syncing Discord roles: ' . $e->getMessage());
            }
        }
    }
}