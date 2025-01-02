<?php
 
namespace OrgManager\Features;
 
abstract class Feature {
    protected string $id;
    protected string $name;
    protected string $description;
    protected array $tags;
    protected bool $enabled;
 
    abstract public function initialize(): void;
    abstract public function register_hooks(): void;
    abstract public function register_rest_routes(): void;
 
    public function is_enabled(): bool {
        return $this->enabled;
    }
 
    public function get_id(): string {
        return $this->id;
    }
 
    public function get_tags(): array {
        return $this->tags;
    }
 
    public function get_name(): string {
        return $this->name;
    }
 
    public function get_description(): string {
        return $this->description;
    }
} 