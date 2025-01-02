<?php

namespace OrgManager\Core;

class BuildInfo {
    private static ?self $instance = null;
    private array $info;
    
    private function __construct() {
        $this->load_build_info();
    }
    
    public static function get_instance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function load_build_info(): void {
        $build_file = org_manager_path . 'build.json';
        
        if (file_exists($build_file)) {
            $this->info = json_decode(file_get_contents($build_file), true) ?? [];
        } else {
            $this->info = [
                'build' => time(),
                'version' => '1.0.0'
            ];
        }
    }
    
    public function get_build(): string {
        return (string) ($this->info['build'] ?? time());
    }
    
    public function get_version(): string {
        return $this->info['version'] ?? '1.0.0';
    }
} 