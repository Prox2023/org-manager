<?php

namespace OrgManager\Features;

use OrgManager\Features\Interfaces\HasSettings;
use OrgManager\Features\Interfaces\HasAdminPage;

class DiscordAuthFeature extends Feature implements HasSettings, HasAdminPage {

    protected string $id = 'discord-auth';
    protected string $name = 'Discord Authentication';
    protected string $description = 'Enables user registration and authentication via Discord';
    protected array $tags = ['authentication', 'discord'];
    protected bool $enabled = true;

    public function initialize(): void {
        // Initialize feature
        add_action('init', [$this, 'init_discord_client']);
    }

    public function register_hooks(): void {
        add_action('wp_login', [$this, 'sync_discord_roles']);
        add_filter('authenticate', [$this, 'authenticate_discord_user'], 10, 3);
    }

    public function register_rest_routes(): void {
        add_action('rest_api_init', function () {
            register_rest_route('org-manager/v1', '/discord/auth', [
                'methods' => 'GET',
                'callback' => [$this, 'handle_discord_callback'],
                'permission_callback' => '__return_true'
            ]);
        });
    }

    public function get_settings(): array {
        return get_option('org_manager_discord_settings', []);
    }

    public function update_settings(array $settings): bool {
        return update_option('org_manager_discord_settings', $settings);
    }

    public function register_settings(): void {
        register_setting('org_manager_discord', 'org_manager_discord_settings');
    }

    public function register_admin_page(): void {
        add_submenu_page(
            'org-manager',
            'Discord Settings',
            'Discord Settings',
            'manage_options',
            'org-manager-discord',
            [$this, 'render_admin_page']
        );
    }

    public function get_menu_position(): int {
        return 10;
    }

    public function get_capability(): string {
        return 'manage_options';
    }

    public function init_discord_client(): void {
        // Initialize Discord client with settings
    }
} 