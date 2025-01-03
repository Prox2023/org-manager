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

    /**
     * Constructor
     * 
     * Initializes the feature by setting up the settings fields.
     */
    public function __construct() {
        $this->init_fields();
    }

    /**
     * Initialize Setting Fields
     * 
     * Sets up the field definitions for the Discord settings form.
     * 
     * @return void
     */
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

    /**
     * Get Field Definitions
     * 
     * Returns an array of field definitions in a format suitable for the frontend.
     * 
     * @return array<mixed> Array of field definitions
     */
    public function get_fields(): array {
        return array_map(fn($field) => $field->to_array(), $this->fields);
    }

    /**
     * Initialize Feature
     * 
     * Sets up WordPress hooks and initializes the Discord client.
     * 
     * @return void
     */
    public function initialize(): void {
        if (WP_DEBUG) {
            error_log('Initializing DiscordAuthFeature');
        }
        add_action('init', [$this, 'init_discord_client']);
        add_action('wp_login', [$this, 'sync_discord_roles'], 10, 2);
        add_filter('authenticate', [$this, 'check_discord_organization'], 30, 3);
        $this->register_rest_routes();
    }

    /**
     * Register WordPress Hooks
     * 
     * Sets up WordPress admin hooks for settings registration.
     * 
     * @return void
     */
    public function register_hooks(): void {
        add_action('admin_init', [$this, 'register_settings']);
    }

    /**
     * Register Settings
     * 
     * Registers WordPress settings and setting fields.
     * 
     * @return void
     */
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
    }

    /**
     * Handle Settings Request
     * 
     * Routes the request to the appropriate handler based on HTTP method.
     * 
     * @param \WP_REST_Request $request The REST request object
     * @return \WP_REST_Response The REST response
     */
    public function handle_settings_request(\WP_REST_Request $request): \WP_REST_Response {
        if ($request->get_method() === 'POST') {
            return $this->handle_update_settings($request);
        }
        return $this->handle_get_settings($request);
    }

    /**
     * Get Settings
     * 
     * Retrieves and returns the current settings with field definitions.
     * 
     * @param \WP_REST_Request $request The REST request object
     * @return \WP_REST_Response The REST response containing settings and fields
     */
    public function handle_get_settings(\WP_REST_Request $request): \WP_REST_Response {
        if (WP_DEBUG) {
            error_log('Handling GET settings request');
        }
        $settings = $this->get_settings();
        $fields = $this->get_fields();
        
        if (WP_DEBUG) {
            error_log('Current settings: ' . print_r($settings, true));
            error_log('Fields definition: ' . print_r($fields, true));
        }
        
        return new \WP_REST_Response([
            'client_id' => $settings['client_id'] ?? '',
            'client_secret' => $settings['client_secret'] ?? '',
            'redirect_uri' => $settings['redirect_uri'] ?? '',
            'registration_enabled' => $settings['registration_enabled'] ?? false,
            'allowed_roles' => $settings['allowed_roles'] ?? [],
            'fields' => $fields
        ], 200);
    }

    /**
     * Update Settings
     * 
     * Handles the settings update request and returns the updated settings.
     * 
     * @param \WP_REST_Request $request The REST request object
     * @return \WP_REST_Response The REST response containing updated settings
     */
    public function handle_update_settings(\WP_REST_Request $request): \WP_REST_Response {
        if (WP_DEBUG) {
            error_log('Handling POST settings request');
            error_log('Request params: ' . print_r($request->get_params(), true));
        }
        
        $params = $request->get_params();
        
        $settings = [
            'client_id' => sanitize_text_field($params['client_id'] ?? ''),
            'client_secret' => sanitize_text_field($params['client_secret'] ?? ''),
            'redirect_uri' => sanitize_text_field($params['redirect_uri'] ?? ''),
            'registration_enabled' => (bool) ($params['registration_enabled'] ?? false),
            'allowed_roles' => []
        ];

        if (WP_DEBUG) {
            error_log('Attempting to save settings: ' . print_r($settings, true));
        }

        $updated = $this->update_settings($settings);

        if (!$updated) {
            if (WP_DEBUG) {
                error_log('Failed to update settings');
            }
            return new \WP_REST_Response([
                'message' => 'Failed to update settings'
            ], 500);
        }

        $fields = $this->get_fields();
        
        return new \WP_REST_Response([
            'message' => 'Settings updated successfully',
            'client_id' => $settings['client_id'],
            'client_secret' => $settings['client_secret'],
            'redirect_uri' => $settings['redirect_uri'],
            'registration_enabled' => $settings['registration_enabled'],
            'allowed_roles' => $settings['allowed_roles'],
            'fields' => $fields
        ], 200);
    }

    /**
     * Synchronize Discord Roles
     * 
     * Syncs Discord roles with WordPress user meta when a user logs in.
     * 
     * @param string $user_login The user's login name
     * @param \WP_User $user WordPress user object
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
}