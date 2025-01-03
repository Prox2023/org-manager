<?php

namespace OrgManager\Core;

/**
 * API Handler Class
 * 
 * Manages REST API routes and responses for the plugin.
 * Provides endpoints for feature and epic management.
 * 
 * @package OrgManager\Core
 */
class Api {
    /** @var array<Epic> Collection of plugin epics */
    private array $epics;

    /**
     * Constructor
     * 
     * @param array<Epic> $epics Collection of plugin epics
     */
    public function __construct(array $epics) {
        $this->epics = $epics;
    }

    /**
     * Register REST Routes
     * 
     * Sets up REST API routes for the plugin.
     * 
     * @return void
     */
    public function register_routes(): void {
        add_action('rest_api_init', function () {
            register_rest_route('org-manager/v1', '/epics', [
                'methods' => 'GET',
                'callback' => [$this, 'get_epics'],
                'permission_callback' => [$this, 'check_permission']
            ]);
        });
    }

    /**
     * Get Epics Endpoint Handler
     * 
     * Returns all epics and their features as JSON.
     * 
     * @return \WP_REST_Response Response containing epic data
     */
    public function get_epics(): \WP_REST_Response {
        $data = array_map(function ($epic) {
            return [
                'id' => $epic->get_id(),
                'name' => $epic->get_name(),
                'tag' => $epic->get_tag(),
                'features' => array_map(function ($feature) {
                    return [
                        'id' => $feature->get_id(),
                        'name' => $feature->get_name(),
                        'description' => $feature->get_description(),
                        'tags' => $feature->get_tags(),
                        'enabled' => $feature->is_enabled()
                    ];
                }, $epic->get_features())
            ];
        }, $this->epics);

        return new \WP_REST_Response($data, 200);
    }

    /**
     * Check API Permission
     * 
     * Verifies if the current user has permission to access the API.
     * 
     * @return bool True if user has permission, false otherwise
     */
    public function check_permission(): bool {
        return current_user_can('manage_options');
    }
} 