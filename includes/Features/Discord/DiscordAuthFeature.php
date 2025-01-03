<?php

namespace OrgManager\Features\Discord;

use OrgManager\Features\Feature;
use OrgManager\Features\Interfaces\HasSettings;
use OrgManager\Features\Interfaces\HasAdminPage;
use OrgManager\Features\Interfaces\Field;
use OrgManager\Features\Fields\SettingField;

/**
 * Discord Authentication Feature
 * 
 * Handles Discord OAuth2 authentication, user registration, and role synchronization.
 * Provides settings management for Discord integration configuration.
 * 
 * @package OrgManager\Features\Discord
 */
class DiscordAuthFeature extends Feature implements HasSettings, HasAdminPage {
    /** @var string Unique identifier for this feature */
    protected string $id = 'discord-auth';
    
    /** @var string Display name of the feature */
    protected string $name = 'Discord Authentication';
    
    /** @var string Feature description */
    protected string $description = 'Enables Discord authentication and organization management';
    
    /** @var array<string> Feature tags for categorization */
    protected array $tags = ['authentication', 'discord', 'organization'];
    
    /** @var bool Whether the feature is enabled */
    protected bool $enabled = true;

    /** @var string WordPress option group for settings */
    private const OPTION_GROUP = 'org_manager_discord';
    
    /** @var string WordPress option name for settings storage */
    private const OPTION_NAME = 'org_manager_discord_settings';
    
    /** @var array<SettingField> Collection of setting fields */
    private array $fields;

    public function __construct() {
        $this->init_fields();
    }

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
        if ($this->is_enabled()) {
            add_action('init', [$this, 'register_settings']);
            add_action('admin_menu', [$this, 'register_admin_page']);
        }
    }

    public function register_rest_routes(): void {
        add_action('rest_api_init', function () {
            register_rest_route('org-manager/v1', '/discord/settings', [
                'methods' => 'GET',
                'callback' => [$this, 'handle_get_settings'],
                'permission_callback' => [$this, 'check_permission']
            ]);

            register_rest_route('org-manager/v1', '/discord/settings', [
                'methods' => 'POST',
                'callback' => [$this, 'handle_update_settings'],
                'permission_callback' => [$this, 'check_permission']
            ]);
        });
    }

    public function get_settings(): array {
        return get_option(self::OPTION_NAME, []);
    }

    public function update_settings(array $settings): bool {
        return update_option(self::OPTION_NAME, $settings);
    }

    public function register_settings(): void {
        register_setting(self::OPTION_GROUP, self::OPTION_NAME);
    }

    public function register_admin_page(): void {
        add_submenu_page(
            'org-manager',
            $this->name,
            $this->name,
            $this->get_capability(),
            'org-manager-' . $this->id,
            [$this, 'render_admin_page']
        );
    }

    public function get_menu_position(): int {
        return 10;
    }

    public function get_capability(): string {
        return 'manage_options';
    }

    public function render_admin_page(): void {
        echo '<div class="wrap">';
        echo '<h1>' . esc_html($this->name) . '</h1>';
        echo '<div id="org-manager-root">
            <div style="display: flex; justify-content: center; padding: 20px;">
                Loading...
            </div>
        </div>';
        echo '</div>';
    }

    private function check_permission(): bool {
        return current_user_can('manage_options');
    }

    public function init_discord_client(): void {
        if (!$this->is_enabled()) {
            return;
        }

        $settings = $this->get_settings();
        if (empty($settings['client_id']) || empty($settings['client_secret'])) {
            return;
        }
    }

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

        $discord_user_id = get_user_meta($user->ID, 'discord_user_id', true);
        if (empty($discord_user_id)) {
            if (WP_DEBUG) {
                error_log('No Discord ID found for user: ' . $user_login);
            }
            return;
        }

        try {
            $discord_client = new DiscordClient(
                $settings['client_id'],
                $settings['client_secret'],
                ''
            );

            $discord_roles = $discord_client->get_user_roles($discord_user_id);
            if (empty($discord_roles)) {
                if (WP_DEBUG) {
                    error_log('No Discord roles found for user: ' . $user_login);
                }
                return;
            }

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

    public function check_discord_organization($user, $username, $password): \WP_User|\WP_Error {
        if (!$user instanceof \WP_User) {
            return $user;
        }

        $settings = $this->get_settings();
        if (empty($settings['client_id']) || empty($settings['client_secret'])) {
            return $user;
        }

        $discord_user_id = get_user_meta($user->ID, 'discord_user_id', true);
        if (empty($discord_user_id)) {
            return new \WP_Error(
                'discord_auth_required',
                'Discord authentication is required.'
            );
        }

        return $user;
    }

    private function init_fields(): void {
        $this->fields = [
            new SettingField(
                Field::TYPE_TEXT,
                'client_id',
                'Client ID',
                'Your Discord application client ID'
            ),
            new SettingField(
                Field::TYPE_PASSWORD,
                'client_secret',
                'Client Secret',
                'Your Discord application client secret'
            ),
            new SettingField(
                Field::TYPE_URL,
                'redirect_uri',
                'Redirect URI',
                'The URL where users will be redirected after Discord authentication',
                get_rest_url(null, 'org-manager/v1/discord/auth')
            ),
            new SettingField(
                Field::TYPE_SWITCH,
                'registration_enabled',
                'Enable Registration',
                'Allow new users to register through Discord',
                false
            )
        ];
    }

    public function get_fields(): array {
        return array_map(fn($field) => $field->to_array(), $this->fields);
    }
}