<?php

namespace OrgManager\Features\Discord;

use OrgManager\Features\Feature;
use OrgManager\Features\Interfaces\HasSettings;
use OrgManager\Features\Interfaces\HasAdminPage;

class DiscordAuthFeature extends Feature implements HasSettings, HasAdminPage {
    protected string $id = 'discord-auth';
    protected string $name = 'Discord Authentication';
    protected string $description = 'Enables user registration and authentication via Discord';
    protected array $tags = ['authentication', 'discord'];
    protected bool $enabled = true;

    public function get_settings(): array {
        return get_option('org_manager_discord_settings', []);
    }

    public function update_settings(array $settings): bool {
        return update_option('org_manager_discord_settings', $settings);
    }

    public function register_settings(): void {
        register_setting('org_manager_discord', 'org_manager_discord_settings');
    }

	public function initialize(): void {
		// TODO: Implement initialize() method.
	}

	public function register_hooks(): void {
		// TODO: Implement register_hooks() method.
	}

	public function register_rest_routes(): void {
		// TODO: Implement register_rest_routes() method.
	}

	public function register_admin_page(): void {
		// TODO: Implement register_admin_page() method.
	}

	public function get_menu_position(): int {
		// TODO: Implement get_menu_position() method.
	}

	public function get_capability(): string {
		// TODO: Implement get_capability() method.
	}
}