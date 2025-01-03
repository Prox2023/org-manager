<?php

namespace OrgManager\CLI;

use WP_CLI;
use WP_CLI_Command;
use function \cli\prompt;
use function \cli\confirm;

class GeneratorCommand extends WP_CLI_Command {
    private string $base_path;

    public function __construct() {
        $this->base_path = org_manager_path;
    }

    /**
     * Creates a new Epic or Feature
     *
     * ## OPTIONS
     *
     * <type>
     * : Type of item to create (epic or feature)
     *
     * [--interactive]
     * : Whether to run in interactive mode
     *
     * ## EXAMPLES
     *
     *     # Create a new Epic interactively
     *     $ wp org-manager generate epic --interactive
     *
     *     # Create a new Feature interactively
     *     $ wp org-manager generate feature --interactive
     *
     * @param array $args
     * @param array $assoc_args
     */
    public function generate($args, $assoc_args) {
        $type = strtolower($args[0]);
        
        if (!in_array($type, ['epic', 'feature'])) {
            WP_CLI::error('Type must be either "epic" or "feature"');
            return;
        }

        if ($type === 'epic') {
            $this->generate_epic();
        } else {
            $this->generate_feature();
        }
    }

    private function generate_epic(): void {
        // Ensure Epics directory exists
        $epics_dir = $this->base_path . 'includes/Epics';
        if (!is_dir($epics_dir)) {
            mkdir($epics_dir, 0755, true);
        }

        $id = \cli\prompt('Epic ID (e.g., administration)');
        $name = \cli\prompt('Epic Name (e.g., Administration)');
        $tag = \cli\prompt('Epic Tag (e.g., admin)');

        $epic_path = $this->base_path . 'includes/Epics/' . $this->pascal_case($id) . 'Epic.php';
        
        $content = $this->get_epic_template($id, $name, $tag);
        
        if (file_put_contents($epic_path, $content)) {
            WP_CLI::success("Epic created at: $epic_path");
        } else {
            WP_CLI::error("Failed to create epic file");
        }
    }

    private function generate_feature(): void {
        $feature_id = WP_CLI\Prompt::get('Feature ID');
        $feature_name = WP_CLI\Prompt::get('Feature Name');
        $feature_description = WP_CLI\Prompt::get('Feature Description');
        $feature_tags = WP_CLI\Prompt::get('Feature Tags (comma-separated)');
        $has_settings = WP_CLI\Prompt::confirm('Does this feature have settings?');
        $has_admin_page = WP_CLI\Prompt::confirm('Does this feature need an admin page?');
        $namespace = WP_CLI\Prompt::get('Feature Namespace');

        $fields = [];
        if ($has_settings) {
            while (WP_CLI\Prompt::confirm('Add Field?', true)) {
                $field_type = WP_CLI\Prompt::get(
                    'Field Type',
                    ['text', 'password', 'checkbox', 'select', 'textarea', 'switch', 'number', 'url']
                );
                
                $field_name = WP_CLI\Prompt::get('Field Name');
                $field_label = WP_CLI\Prompt::get('Field Label');
                $field_description = WP_CLI\Prompt::get('Field Description');
                $default_value = WP_CLI\Prompt::get('Default Value', ['optional' => true]);

                $options = [];
                if ($field_type === 'select') {
                    $options_string = WP_CLI\Prompt::get('Options (comma-separated key:value pairs)');
                    $pairs = explode(',', $options_string);
                    foreach ($pairs as $pair) {
                        list($key, $value) = explode(':', trim($pair));
                        $options[trim($key)] = trim($value);
                    }
                }

                $fields[] = [
                    'type' => $field_type,
                    'name' => $field_name,
                    'label' => $field_label,
                    'description' => $field_description,
                    'default' => $default_value,
                    'options' => $options
                ];
            }
        }

        // Generate the feature code with fields
        $feature_code = $this->generate_feature_code(
            $feature_id,
            $feature_name,
            $feature_description,
            $feature_tags,
            $has_settings,
            $has_admin_page,
            $namespace,
            $fields
        );

        $feature_dir = $this->base_path . 'includes/Features/' . $namespace;
        if (!is_dir($feature_dir)) {
            mkdir($feature_dir, 0755, true);
        }

        $feature_path = $feature_dir . '/' . $this->pascal_case($feature_id) . 'Feature.php';
        
        if (file_put_contents($feature_path, $feature_code)) {
            WP_CLI::success("Feature created at: $feature_path");
            
            if ($has_settings) {
                WP_CLI::log("Don't forget to register your settings in register_settings()");
            }
            if ($has_admin_page) {
                WP_CLI::log("Don't forget to implement your admin page in register_admin_page()");
            }
        } else {
            WP_CLI::error("Failed to create feature file");
        }
    }

    private function generate_feature_code(
        string $id,
        string $name,
        string $description,
        string $tags,
        bool $has_settings,
        bool $has_admin_page,
        string $namespace,
        array $fields = []
    ): string {
        $implements = [];
        if ($has_settings) $implements[] = 'HasSettings';
        if ($has_admin_page) $implements[] = 'HasAdminPage';

        $fields_code = '';
        if (!empty($fields)) {
            $fields_code = "\n    private function init_fields(): void {\n";
            $fields_code .= "        \$this->fields = [\n";
            foreach ($fields as $field) {
                $fields_code .= $this->generate_field_code($field);
            }
            $fields_code .= "        ];\n    }";
        }

        $tags_array = array_map('trim', explode(',', $tags));
        $tags_php = "['" . implode("', '", $tags_array) . "']";
        
        $use_statements = [];
        
        if ($has_settings) {
            $use_statements[] = 'use OrgManager\Features\Interfaces\HasSettings;';
        }
        if ($has_admin_page) {
            $use_statements[] = 'use OrgManager\Features\Interfaces\HasAdminPage;';
        }
        
        $use_statements = implode("\n", $use_statements);
        
        $template = <<<PHP
<?php

namespace OrgManager\Features\\$namespace;

use OrgManager\Features\Feature;
$use_statements

class {$this->pascal_case($id)}Feature extends Feature$implements_string {
    protected string \$id = '$id';
    protected string \$name = '$name';
    protected string \$description = '$description';
    protected array \$tags = $tags_php;
    protected bool \$enabled = true;

    public function initialize(): void {
        // Initialize your feature here
    }

    public function register_hooks(): void {
        // Register WordPress hooks here
    }

    public function register_rest_routes(): void {
        // Register REST API routes here
        add_action('rest_api_init', function () {
            register_rest_route('org-manager/v1', '/$id', [
                'methods' => 'GET',
                'callback' => [\$this, 'handle_request'],
                'permission_callback' => [\$this, 'check_permission']
            ]);
        });
    }

PHP;

        if ($has_settings) {
            $template .= <<<PHP
            
    public function get_settings(): array {
        return get_option('org_manager_{$id}_settings', []);
    }

    public function update_settings(array \$settings): bool {
        return update_option('org_manager_{$id}_settings', \$settings);
    }

    public function register_settings(): void {
        register_setting('org_manager_{$id}', 'org_manager_{$id}_settings');
    }

PHP;
        }

        if ($has_admin_page) {
            $template .= <<<PHP
            
    public function register_admin_page(): void {
        add_submenu_page(
            'org-manager-features',
            '$name',
            '$name',
            \$this->get_capability(),
            'org-manager-$id',
            [\$this, 'render_admin_page']
        );
    }

    public function get_menu_position(): int {
        return 10;
    }

    public function get_capability(): string {
        return 'manage_options';
    }

    public function render_admin_page(): void {
        // Render your admin page here
        echo '<div class="wrap"><h1>$name</h1></div>';
    }

PHP;
        }

        $template .= "}\n";
        return $template;
    }

    private function generate_field_code(array $field): string {
        $default = $field['default'] !== '' ? ", {$field['default']}" : '';
        $options = !empty($field['options']) ? ", " . var_export($field['options'], true) : '';

        return <<<PHP
            new SettingField(
                Field::TYPE_{$this->get_field_constant($field['type'])},
                '{$field['name']}',
                '{$field['label']}',
                '{$field['description']}'{$default}{$options}
            ),

PHP;
    }

    private function get_field_constant(string $type): string {
        return strtoupper($type);
    }

    private function pascal_case(string $string): string {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $string)));
    }
} 