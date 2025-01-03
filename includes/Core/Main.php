<?php

namespace OrgManager\Core;

use OrgManager\Core\Admin\FeaturesPage;
use OrgManager\Epics\AdministrationEpic;
use OrgManager\Epics\FleetManagerEpic;

/**
 * Main Plugin Class
 * 
 * Core class responsible for initializing the plugin, loading epics,
 * and setting up the admin interface.
 * 
 * @package OrgManager\Core
 */
class Main {
    /** @var Admin|null Admin interface handler */
    private $admin;

    /** @var Api|null API handler */
    private $api;

    /** @var array<Epic> Collection of plugin epics */
    private array $epics = [];

    /** @var FeaturesPage Features page handler */
    private FeaturesPage $features_page;

    /**
     * Initialize Plugin
     * 
     * Sets up the plugin by initializing epics, API, and WordPress hooks.
     * 
     * @return void
     */
    public function init(): void {
        $this->features_page = new FeaturesPage();
        $this->initialize_epics();
        $this->initialize_api();

        add_action('init', [$this, 'init_hooks']);
    }

    /**
     * Initialize Epics
     * 
     * Creates and initializes all plugin epics.
     * 
     * @return void
     */
    private function initialize_epics(): void {
        $admin_epic = new AdministrationEpic();
        $admin_epic->initialize();
        $this->epics[] = $admin_epic;

        $fleet_epic = new FleetManagerEpic();
        $fleet_epic->initialize();
        $this->epics[] = $fleet_epic;
    }

    /**
     * Initialize API
     * 
     * Sets up the REST API handler.
     * 
     * @return void
     */
    private function initialize_api(): void {
        $this->api = new Api($this->epics);
        $this->api->register_routes();
    }

    /**
     * Initialize WordPress Hooks
     * 
     * Registers WordPress actions and filters.
     * 
     * @return void
     */
    public function init_hooks(): void {
        // Register the main plugin page first
        add_action('admin_menu', function() {
            add_menu_page(
                'Organization Manager',
                'Organization Manager',
                'manage_options',
                'org-manager',
                [$this->features_page, 'render'],
                'dashicons-admin-generic',
                30
            );
        });

        // Then register feature pages
        add_action('admin_menu', [$this->features_page, 'register']);
    }
} 