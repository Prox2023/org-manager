<?php

namespace OrgManager\Core;

use OrgManager\Core\Admin\FeaturesPage;
use OrgManager\Epics\AdministrationEpic;
use OrgManager\Epics\FleetManagerEpic;

class Main {
    private $admin;
    private $api;
    private array $epics = [];
    private FeaturesPage $features_page;

    public function init(): void {
        $this->features_page = new FeaturesPage();
        $this->initialize_epics();
        $this->initialize_api();

        add_action('init', [$this, 'init_hooks']);
    }

    private function initialize_epics(): void {
        $admin_epic = new AdministrationEpic();
        $admin_epic->initialize();
        $this->epics[] = $admin_epic;
		$fleet_epic = new FleetManagerEpic();
		$fleet_epic->initialize();
		$this->epics[] = $fleet_epic;
    }

    private function initialize_api(): void {
        $this->api = new Api($this->epics);
        $this->api->register_routes();
    }

    public function init_hooks(): void {
        add_action('admin_menu', [$this->features_page, 'register']);
    }
} 