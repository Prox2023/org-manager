<?php

namespace OrgManager\Features;

/**
 * Epic Class
 * 
 * Represents a collection of related features. Epics are used to organize
 * and group features together under a common theme or purpose.
 * 
 * @package OrgManager\Features
 */
class Epic {
    /** @var string Epic identifier */
    private string $id;

    /** @var string Epic display name */
    private string $name;

    /** @var string Epic tag for categorization */
    private string $tag;

    /** @var array<Feature> Collection of features in this epic */
    private array $features = [];

    /**
     * Constructor
     * 
     * @param string $id   Epic identifier
     * @param string $name Epic display name
     * @param string $tag  Epic tag for categorization
     */
    public function __construct(string $id, string $name, string $tag) {
        $this->id = $id;
        $this->name = $name;
        $this->tag = $tag;
    }

    /**
     * Add Feature
     * 
     * Adds a feature to this epic's collection.
     * 
     * @param Feature $feature Feature to add
     * @return void
     */
    public function add_feature(Feature $feature): void {
        $this->features[] = $feature;
    }

    /**
     * Initialize Epic
     * 
     * Initializes all enabled features in this epic.
     * 
     * @return void
     */
    public function initialize(): void {
        foreach ($this->features as $feature) {
            if ($feature->is_enabled()) {
                $feature->initialize();
                $feature->register_hooks();
                $feature->register_rest_routes();
            }
        }
    }

    /**
     * Get Features
     * 
     * @return array<Feature> Array of features in this epic
     */
    public function get_features(): array {
        return $this->features;
    }

    /**
     * Get Epic ID
     * 
     * @return string Epic identifier
     */
    public function get_id(): string {
        return $this->id;
    }

    /**
     * Get Epic Name
     * 
     * @return string Epic display name
     */
    public function get_name(): string {
        return $this->name;
    }

    /**
     * Get Epic Tag
     * 
     * @return string Epic tag
     */
    public function get_tag(): string {
        return $this->tag;
    }
} 