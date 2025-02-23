<?php

namespace OrgManager\Core\Admin;

use OrgManager\Core\BuildInfo;

abstract class AdminPage {
    protected string $page_title;
    protected string $menu_title;
    protected string $capability;
    protected string $menu_slug;
    protected int $position;

    public function register(): void {
        add_menu_page(
            $this->page_title,
            $this->menu_title,
            $this->capability,
            $this->menu_slug,
            [$this, 'render'],
            'dashicons-admin-generic',
            $this->position
        );

        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function enqueue_assets(string $hook): void {
        if ("toplevel_page_{$this->menu_slug}" !== $hook) {
            return;
        }

        wp_enqueue_script('wp-element');
        remove_action('admin_print_scripts', 'print_emoji_detection_script');

        $asset_file = org_manager_path . 'admin/js/dist/manifest.json';
        if (file_exists($asset_file)) {
            $manifest = json_decode(file_get_contents($asset_file), true);
            $main_file = $manifest['src/index.tsx']['file'] ?? 'main.js';
        } else {
            $main_file = 'main.js';
        }

        wp_enqueue_script(
            'org-manager-admin',
            org_manager_url . 'admin/js/dist/' . $main_file,
            ['wp-element'],
            BuildInfo::get_instance()->get_build(),
            true
        );

        $nonce = wp_create_nonce('wp_rest');
        wp_localize_script('org-manager-admin', 'orgManagerData', [
            'apiUrl' => rest_url('org-manager/v1'),
            'nonce' => $nonce,
            'debug' => WP_DEBUG
        ]);

        if (WP_DEBUG) {
            error_log('REST API URL: ' . rest_url('org-manager/v1'));
            error_log('Nonce: ' . $nonce);
        }
    }

    public function render(): void {
        ?>
        <div class="wrap">
            <div id="org-manager-root">
                <div style="display: flex; justify-content: center; padding: 20px;">
                    Loading...
                </div>
            </div>
        </div>
        <?php
    }
} 