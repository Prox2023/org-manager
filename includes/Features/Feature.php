<?php
 
namespace OrgManager\Features;
 
/**
 * Abstract Feature Base Class
 * 
 * Base class for all plugin features. Provides common functionality and
 * required abstract methods that all features must implement.
 * 
 * @package OrgManager\Features
 */
abstract class Feature {
    /** @var string Unique identifier for the feature */
    protected string $id;

    /** @var string Display name of the feature */
    protected string $name;

    /** @var string Feature description */
    protected string $description;

    /** @var array<string> Feature tags for categorization */
    protected array $tags;

    /** @var bool Whether the feature is enabled */
    protected bool $enabled;

    /**
     * Initialize Feature
     * 
     * Sets up the feature by registering necessary hooks and initializing components.
     * Must be implemented by concrete features.
     * 
     * @return void
     */
    abstract public function initialize(): void;

    /**
     * Register WordPress Hooks
     * 
     * Registers any WordPress actions and filters needed by the feature.
     * Must be implemented by concrete features.
     * 
     * @return void
     */
    abstract public function register_hooks(): void;

    /**
     * Register REST Routes
     * 
     * Registers any REST API routes needed by the feature.
     * Must be implemented by concrete features.
     * 
     * @return void
     */
    abstract public function register_rest_routes(): void;

    /**
     * Check if Feature is Enabled
     * 
     * @return bool True if feature is enabled, false otherwise
     */
    public function is_enabled(): bool {
        return $this->enabled;
    }

    /**
     * Get Feature ID
     * 
     * @return string Feature identifier
     */
    public function get_id(): string {
        return $this->id;
    }

    /**
     * Get Feature Tags
     * 
     * @return array<string> Array of feature tags
     */
    public function get_tags(): array {
        return $this->tags;
    }

    /**
     * Get Feature Name
     * 
     * @return string Feature display name
     */
    public function get_name(): string {
        return $this->name;
    }

    /**
     * Get Feature Description
     * 
     * @return string Feature description
     */
    public function get_description(): string {
        return $this->description;
    }
} 