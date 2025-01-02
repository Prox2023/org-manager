<?php

namespace OrgManager\Features;

class Epic {
    private string $id;
    private string $name;
    private string $tag;
    private array $features = [];

    public function __construct(string $id, string $name, string $tag) {
        $this->id = $id;
        $this->name = $name;
        $this->tag = $tag;
    }

    public function add_feature(Feature $feature): void {
        $this->features[] = $feature;
    }

    public function initialize(): void {
        foreach ($this->features as $feature) {
            if ($feature->is_enabled()) {
                $feature->initialize();
                $feature->register_hooks();
                $feature->register_rest_routes();
            }
        }
    }

    public function get_features(): array {
        return $this->features;
    }

    public function get_id(): string {
        return $this->id;
    }

    public function get_name(): string {
        return $this->name;
    }

    public function get_tag(): string {
        return $this->tag;
    }
} 