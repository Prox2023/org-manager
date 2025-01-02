<?php

namespace OrgManager\Features\Interfaces;

interface HasSettings {
    public function get_settings(): array;
    public function update_settings(array $settings): bool;
    public function register_settings(): void;
} 