<?php

namespace OrgManager\Core\Admin;

class FeaturesPage extends AdminPage {
    public function __construct() {
        $this->page_title = 'Organization Manager';
        $this->menu_title = 'Organization Manager';
        $this->capability = 'manage_options';
        $this->menu_slug = 'org-manager';
        $this->position = 30;
    }

    public function render(): void {
        ?>
        <div class="wrap">
            <h1>Organization Manager</h1>
            <div id="org-manager-root">
                <div style="display: flex; justify-content: center; padding: 20px;">
                    Loading...
                </div>
            </div>
        </div>
        <?php
    }
} 