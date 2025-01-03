<?php

namespace OrgManager\Features\Fields;

use OrgManager\Features\Interfaces\Field;

/**
 * Setting Field Implementation
 * 
 * Implements the Field interface for settings form fields.
 * Provides a concrete implementation for form field handling.
 * 
 * @package OrgManager\Features\Fields
 */
class SettingField implements Field {
    /** @var string Field type identifier */
    private string $type;
    
    /** @var string Field name/identifier */
    private string $name;
    
    /** @var string Field label */
    private string $label;
    
    /** @var string|null Field description */
    private ?string $description;
    
    /** @var mixed Default field value */
    private mixed $default_value;
    
    /** @var array<string, mixed> Field options for select/radio fields */
    private array $options;

    /**
     * Constructor
     * 
     * @param string      $type          Field type identifier
     * @param string      $name          Field name/identifier
     * @param string      $label         Field label
     * @param string|null $description   Field description
     * @param mixed       $default_value Default field value
     * @param array       $options       Field options for select/radio fields
     */
    public function __construct(
        string $type,
        string $name,
        string $label,
        ?string $description = null,
        mixed $default_value = null,
        array $options = []
    ) {
        $this->type = $type;
        $this->name = $name;
        $this->label = $label;
        $this->description = $description;
        $this->default_value = $default_value;
        $this->options = $options;
    }

    /**
     * {@inheritDoc}
     */
    public function get_type(): string {
        return $this->type;
    }

    /**
     * {@inheritDoc}
     */
    public function get_name(): string {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function get_label(): string {
        return $this->label;
    }

    /**
     * {@inheritDoc}
     */
    public function get_description(): ?string {
        return $this->description;
    }

    /**
     * {@inheritDoc}
     */
    public function get_default_value(): mixed {
        return $this->default_value;
    }

    /**
     * {@inheritDoc}
     */
    public function get_options(): array {
        return $this->options;
    }

    /**
     * Convert Field to Array
     * 
     * Converts the field object to an array format suitable for frontend use.
     * 
     * @return array<string, mixed> Field data as array
     */
    public function to_array(): array {
        return [
            'type' => $this->type,
            'name' => $this->name,
            'label' => $this->label,
            'description' => $this->description,
            'defaultValue' => $this->default_value,
            'options' => $this->options
        ];
    }
} 