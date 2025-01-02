<?php

namespace OrgManager\Core\Admin;

class FeaturesPage extends AdminPage {
    public function __construct() {
        $this->page_title = 'Features';
        $this->menu_title = 'Org Manager';
        $this->capability = 'manage_options';
        $this->menu_slug = 'org-manager-features';
        $this->position = 30;
    }
} 