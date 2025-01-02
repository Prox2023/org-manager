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
        $id = \cli\prompt('Feature ID (e.g., discord-auth)');
        $name = \cli\prompt('Feature Name (e.g., Discord Authentication)');
        $description = \cli\prompt('Feature Description');
        $tags = \cli\prompt('Feature Tags (comma-separated)');
        
        $has_settings = \cli\confirm('Does this feature have settings?', true);
        $has_admin_page = \cli\confirm('Does this feature need an admin page?', true);
        
        $namespace = \cli\prompt('Feature Namespace (e.g., Discord)', 'Core');
        
        $feature_dir = $this->base_path . 'includes/Features/' . $namespace;
        if (!is_dir($feature_dir)) {
            mkdir($feature_dir, 0755, true);
        }

        $feature_path = $feature_dir . '/' . $this->pascal_case($id) . 'Feature.php';
        
        $content = $this->get_feature_template(
            $id,
            $name,
            $description,
            $tags,
            $namespace,
            $has_settings,
            $has_admin_page
        );
        
        if (file_put_contents($feature_path, $content)) {
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

    private function get_epic_template(string $id, string $name, string $tag): string {
        return <<<PHP
<?php

namespace OrgManager\Epics;

use OrgManager\Features\Epic;

class {$this->pascal_case($id)}Epic extends Epic {
    public function __construct() {
        parent::__construct(
            '$id',
            '$name',
            '$tag'
        );

        // Add your features here
        // \$this->add_feature(new SomeFeature());
    }
}

PHP;
    }

    private function get_feature_template(
        string $id,
        string $name,
        string $description,
        string $tags,
        string $namespace,
        bool $has_settings,
        bool $has_admin_page
    ): string {
        $tags_array = array_map('trim', explode(',', $tags));
        $tags_php = "['" . implode("', '", $tags_array) . "']";
        
        $implements = [];
        if ($has_settings) $implements[] = 'HasSettings';
        if ($has_admin_page) $implements[] = 'HasAdminPage';
        
        $implements_string = $implements ? ' implements ' . implode(', ', $implements) : '';
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

    private function pascal_case(string $string): string {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $string)));
    }
} 