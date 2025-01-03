<?php

namespace OrgManager\Features\Interfaces;

/**
 * Field Interface
 * 
 * Defines the contract for form field implementations in the plugin.
 * Provides field type constants and required methods for field handling.
 * 
 * @package OrgManager\Features\Interfaces
 */
interface Field {
    /** @var string Text input field type */
    public const TYPE_TEXT = 'text';
    
    /** @var string Password input field type */
    public const TYPE_PASSWORD = 'password';
    
    /** @var string Checkbox input field type */
    public const TYPE_CHECKBOX = 'checkbox';
    
    /** @var string Select dropdown field type */
    public const TYPE_SELECT = 'select';
    
    /** @var string Textarea field type */
    public const TYPE_TEXTAREA = 'textarea';
    
    /** @var string Switch toggle field type */
    public const TYPE_SWITCH = 'switch';
    
    /** @var string Number input field type */
    public const TYPE_NUMBER = 'number';
    
    /** @var string URL input field type */
    public const TYPE_URL = 'url';
    
    /**
     * Get Field Type
     * 
     * Returns the type of the field (e.g., text, password, switch).
     * 
     * @return string Field type identifier
     */
    public function get_type(): string;

    /**
     * Get Field Name
     * 
     * Returns the name/identifier of the field used in forms.
     * 
     * @return string Field name
     */
    public function get_name(): string;

    /**
     * Get Field Label
     * 
     * Returns the human-readable label for the field.
     * 
     * @return string Field label
     */
    public function get_label(): string;

    /**
     * Get Field Description
     * 
     * Returns the help text/description for the field.
     * 
     * @return string|null Field description or null if none
     */
    public function get_description(): ?string;

    /**
     * Get Default Value
     * 
     * Returns the default value for the field.
     * 
     * @return mixed Default field value
     */
    public function get_default_value(): mixed;

    /**
     * Get Field Options
     * 
     * Returns available options for select/radio fields.
     * 
     * @return array<string, mixed> Field options
     */
    public function get_options(): array;
} 