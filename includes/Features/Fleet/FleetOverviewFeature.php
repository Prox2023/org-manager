<?php

namespace OrgManager\Features\Fleet;

use OrgManager\Features\Feature;
use OrgManager\Features\Interfaces\HasSettings;
use OrgManager\Features\Interfaces\HasAdminPage;

class FleetOverviewFeature extends Feature implements HasSettings, HasAdminPage {
    protected string $id = 'fleet-overview';
    protected string $name = 'Fleet Overview';
    protected string $description = 'Displays all current Ships in the Organisational Fleet';
    protected array $tags = ['fleet-manager'];
    protected bool $enabled = true;

    public function initialize(): void {
        // Initialize your feature here
    }

    public function register_hooks(): void {
        // Register WordPress hooks here
    }

    public function register_rest_routes(): void {
        // Register REST API routes here
        add_action('rest_api_init', function () {
            register_rest_route('org-manager/v1', '/fleet-overview', [
                'methods' => 'GET',
                'callback' => [$this, 'handle_request'],
                'permission_callback' => [$this, 'check_permission']
            ]);
        });
    }
            
    public function get_settings(): array {
        return get_option('org_manager_fleet-overview_settings', []);
    }

    public function update_settings(array $settings): bool {
        return update_option('org_manager_fleet-overview_settings', $settings);
    }

    public function register_settings(): void {
        register_setting('org_manager_fleet-overview', 'org_manager_fleet-overview_settings');
    }
            
    public function register_admin_page(): void {
        add_submenu_page(
            'org-manager-features',
            'Fleet Overview',
            'Fleet Overview',
            $this->get_capability(),
            'org-manager-fleet-overview',
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
        // Render your admin page here
        echo '<div class="wrap"><h1>Fleet Overview</h1></div>';
    }
}
