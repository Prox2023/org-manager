<?php

namespace OrgManager\Tests\Features\Discord;

use OrgManager\Tests\TestCase;
use OrgManager\Features\Discord\DiscordAuthFeature;
use OrgManager\Features\Discord\DiscordClient;
use WP_REST_Request;
use WP_REST_Response;

class DiscordAuthFeatureTest extends TestCase {
    private DiscordAuthFeature $feature;

    protected function setUp(): void {
        parent::setUp();
        $this->feature = new DiscordAuthFeature();
        $this->feature->initialize();
    }

    public function test_feature_initialization(): void {
        // Test that all required hooks are registered
        $this->assertHasAction('init', [$this->feature, 'init_discord_client']);
        $this->assertHasAction('wp_login', [$this->feature, 'sync_discord_roles']);
        $this->assertHasFilter('authenticate', [$this->feature, 'check_discord_organization']);
    }

    public function test_settings_management(): void {
        // Test settings CRUD operations
        $settings = [
            'client_id' => 'test_client_id',
            'client_secret' => 'test_client_secret',
            'redirect_uri' => 'http://example.com/callback',
            'registration_enabled' => true,
            'allowed_roles' => ['123456']
        ];

        // Update settings
        $this->assertTrue($this->feature->update_settings($settings));

        // Get settings
        $stored_settings = $this->feature->get_settings();
        $this->assertEquals($settings, $stored_settings);
    }

    public function test_rest_api_endpoints(): void {
        // Test GET settings endpoint
        $request = new WP_REST_Request('GET', '/org-manager/v1/discord/settings');
        $response = $this->feature->handle_get_settings($request);

        $this->assertInstanceOf(WP_REST_Response::class, $response);
        $this->assertEquals(200, $response->get_status());

        $data = $response->get_data();
        $this->assertArrayHasKey('fields', $data);
        $this->assertIsArray($data['fields']);

        // Test POST settings endpoint
        $new_settings = [
            'client_id' => 'new_client_id',
            'client_secret' => 'new_client_secret',
            'redirect_uri' => 'http://example.com/new-callback',
            'registration_enabled' => false
        ];

        $request = new WP_REST_Request('POST', '/org-manager/v1/discord/settings');
        $request->set_body_params($new_settings);

        $response = $this->feature->handle_update_settings($request);
        $this->assertEquals(200, $response->get_status());

        // Verify settings were updated
        $updated_settings = $this->feature->get_settings();
        foreach ($new_settings as $key => $value) {
            $this->assertEquals($value, $updated_settings[$key]);
        }
    }

    public function test_discord_role_sync(): void {
        // Create test user
        $user_id = $this->factory->user->create([
            'user_login' => 'test_user'
        ]);
        $user = get_user_by('ID', $user_id);

        // Add Discord user ID
        update_user_meta($user_id, 'discord_user_id', '123456789');

        // Configure settings
        $this->feature->update_settings([
            'client_id' => 'test_client_id',
            'client_secret' => 'test_client_secret'
        ]);

        // Mock Discord client
        $discord_client = $this->createMock(DiscordClient::class);
        $discord_client->method('get_user_roles')
            ->willReturn(['role1', 'role2']);

        // Test role sync
        $this->feature->sync_discord_roles('test_user', $user);

        // Verify roles were stored
        $stored_roles = get_user_meta($user_id, 'discord_roles', true);
        $this->assertEquals(['role1', 'role2'], $stored_roles);
    }
} 