<?php

namespace OrgManager\Features\Interfaces;

interface HasAdminPage {
    public function register_admin_page(): void;
    public function get_menu_position(): int;
    public function get_capability(): string;
} 