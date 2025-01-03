<?php

namespace OrgManager\Core\Admin;

use OrgManager\Core\BuildInfo;

/**
 * Admin Page Base Class
 * 
 * Base class for WordPress admin pages. Provides common functionality
 * for registering and rendering admin pages.
 * 
 * @package OrgManager\Core\Admin
 */
abstract class AdminPage {
    /** @var string Page title */
    protected string $page_title;

    /** @var string Menu title */
    protected string $menu_title;

    /** @var string Required capability to access page */
    protected string $capability;

    /** @var string Menu slug */
    protected string $menu_slug;

    /** @var int Menu position */
    protected int $position;

    /**
     * Register Admin Page
     * 
     * Registers the admin page with WordPress and sets up assets.
     * 
     * @return void
     */
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

    /**
     * Enqueue Assets
     * 
     * Loads required scripts and styles for the admin page.
     * 
     * @param string $hook Current admin page hook
     * @return void
     */
    public function enqueue_assets(string $hook): void {
        if (WP_DEBUG) {
            error_log('Current admin page hook: ' . $hook);
            error_log('Expected hook: toplevel_page_' . $this->menu_slug);
        }

        if ("toplevel_page_{$this->menu_slug}" !== $hook) {
            return;
        }

        if (WP_DEBUG) {
            error_log('Loading admin assets');
        }

        wp_enqueue_script('wp-element');
        remove_action('admin_print_scripts', 'print_emoji_detection_script');

        $asset_file = org_manager_path . 'admin/js/dist/manifest.json';
        if (WP_DEBUG) {
            error_log('Asset file path: ' . $asset_file);
            error_log('Asset file exists: ' . (file_exists($asset_file) ? 'yes' : 'no'));
        }

        if (file_exists($asset_file)) {
            $manifest = json_decode(file_get_contents($asset_file), true);
            $main_file = $manifest['src/index.tsx']['file'] ?? 'main.js';
            if (WP_DEBUG) {
                error_log('Main file from manifest: ' . $main_file);
            }
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
    }

    /**
     * Render Admin Page
     * 
     * Outputs the HTML for the admin page.
     * 
     * @return void
     */
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