# Organization Manager CLI Commands

WP-CLI commands for the Organization Manager plugin development.

## Prerequisites

- WordPress installed
- WP-CLI installed (`wp --info` to verify)
- Organization Manager plugin activated

## Commands Overview

### Generate Epic

Creates a new Epic with boilerplate code.

```bash
wp org-manager generate epic --interactive
```

#### Interactive Prompts
- **Epic ID**: Lowercase, hyphen-separated identifier (e.g., `fleet-management`)
- **Epic Name**: Human-readable name (e.g., `Fleet Management`)
- **Epic Tag**: Short identifier for grouping (e.g., `fleet`)

#### Example Usage
```bash
$ wp org-manager generate epic --interactive
Epic ID (e.g., administration): fleet-management
Epic Name (e.g., Administration): Fleet Management
Epic Tag (e.g., admin): fleet
Success: Epic created at: includes/Epics/FleetManagementEpic.php
```

### Generate Feature

Creates a new Feature with boilerplate code and field configuration.

```bash
wp org-manager generate feature --interactive
```

#### Interactive Prompts
- **Feature ID**: Lowercase, hyphen-separated identifier (e.g., `discord-auth`)
- **Feature Name**: Human-readable name (e.g., `Discord Authentication`)
- **Feature Description**: Detailed description of functionality
- **Feature Tags**: Comma-separated list (e.g., `auth,discord,integration`)
- **Has Settings**: Whether feature needs settings [Y/n]
- **Has Admin Page**: Whether feature needs admin interface [Y/n]
- **Feature Namespace**: PHP namespace segment (e.g., `Discord`)

If "Has Settings" is Yes, you'll be prompted to configure fields:

#### Field Configuration
- **Add Field? [Y/n]**: Whether to add a new field
- If Yes:
  - **Field Type**: Select from available types:
    - `text`: Text input field
    - `password`: Password input field
    - `checkbox`: Checkbox field
    - `select`: Dropdown select field
    - `textarea`: Multi-line text field
    - `switch`: Toggle switch field
    - `number`: Number input field
    - `url`: URL input field
  - **Field Name**: Identifier for the field (e.g., `client_id`)
  - **Field Label**: Display label (e.g., `Client ID`)
  - **Field Description**: Help text shown below the field
  - **Default Value**: Initial value (optional)
  - If type is `select`:
    - **Options**: Comma-separated list of key:value pairs (e.g., `key1:Label 1,key2:Label 2`)

#### Example Usage with Fields
```bash
$ wp org-manager generate feature --interactive
Feature ID: discord-auth
Feature Name: Discord Authentication
Feature Description: Enables Discord authentication and role sync
Feature Tags: auth,discord,integration
Has Settings? [Y/n] Y
Has Admin Page? [Y/n] Y
Feature Namespace: Discord

Add Field? [Y/n] Y
Field Type [text,password,checkbox,select,textarea,switch,number,url]: text
Field Name: client_id
Field Label: Client ID
Field Description: Your Discord application client ID
Default Value: 

Add Field? [Y/n] Y
Field Type [text,password,checkbox,select,textarea,switch,number,url]: password
Field Name: client_secret
Field Label: Client Secret
Field Description: Your Discord application client secret
Default Value:

Add Field? [Y/n] Y
Field Type [text,password,checkbox,select,textarea,switch,number,url]: switch
Field Name: registration_enabled
Field Label: Enable Registration
Field Description: Allow new users to register through Discord
Default Value: false

Add Field? [Y/n] n
Success: Feature created at: includes/Features/Discord/DiscordAuthFeature.php
```

## Generated Files

### Epic Structure
```php
namespace OrgManager\Epics;

use OrgManager\Features\Epic;

class FleetManagementEpic extends Epic {
    public function __construct() {
        parent::__construct(
            'fleet-management',
            'Fleet Management',
            'fleet'
        );
    }
}
```

### Feature Structure
```php
namespace OrgManager\Features\Discord;

use OrgManager\Features\Feature;
use OrgManager\Features\Interfaces\HasSettings;
use OrgManager\Features\Interfaces\HasAdminPage;
use OrgManager\Features\Interfaces\Field;
use OrgManager\Features\Fields\SettingField;

class DiscordAuthFeature extends Feature implements HasSettings, HasAdminPage {
    protected string $id = 'discord-auth';
    protected string $name = 'Discord Authentication';
    protected string $description = 'Enables Discord authentication and role sync';
    protected array $tags = ['auth', 'discord', 'integration'];
    protected bool $enabled = true;

    private array $fields;

    public function __construct() {
        $this->init_fields();
    }

    private function init_fields(): void {
        $this->fields = [
            new SettingField(
                Field::TYPE_TEXT,
                'client_id',
                'Client ID',
                'Your Discord application client ID'
            ),
            new SettingField(
                Field::TYPE_PASSWORD,
                'client_secret',
                'Client Secret',
                'Your Discord application client secret'
            ),
            new SettingField(
                Field::TYPE_SWITCH,
                'registration_enabled',
                'Enable Registration',
                'Allow new users to register through Discord',
                false
            )
        ];
    }
}
```

## File Locations

Generated files are placed in:
- Epics: `includes/Epics/{EpicName}Epic.php`
- Features: `includes/Features/{Namespace}/{FeatureName}Feature.php`

## Best Practices

1. **Naming Conventions**
   - Use hyphen-separated IDs (`feature-name`)
   - Use PascalCase for class names (`FeatureName`)
   - Use descriptive, clear names

2. **Organization**
   - Group related features under appropriate epics
   - Use meaningful tags for categorization
   - Place features in appropriate namespaces

3. **Implementation**
   - Implement all required interface methods
   - Add proper PHPDoc blocks
   - Follow WordPress coding standards