<?php

namespace OrgManager\Core;

class Api {
    private array $epics;

    public function __construct(array $epics) {
        $this->epics = $epics;
    }

    public function register_routes(): void {
        add_action('rest_api_init', function () {
            register_rest_route('org-manager/v1', '/epics', [
                'methods' => 'GET',
                'callback' => [$this, 'get_epics'],
                'permission_callback' => [$this, 'check_permission']
            ]);
        });
    }

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

    public function check_permission(): bool {
        return current_user_can('manage_options');
    }
} 