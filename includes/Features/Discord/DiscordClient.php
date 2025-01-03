<?php

namespace OrgManager\Features\Discord;

/**
 * Discord API Client
 * 
 * Handles communication with Discord's API for authentication, user management,
 * and role synchronization.
 * 
 * @package OrgManager\Features\Discord
 */
class DiscordClient {
    /** @var string Discord application client ID */
    private string $client_id;

    /** @var string Discord application client secret */
    private string $client_secret;

    /** @var string Discord guild (server) ID */
    private string $guild_id;

    /** @var string|null Discord OAuth2 access token */
    private ?string $access_token = null;

    /**
     * Constructor
     * 
     * @param string $client_id     Discord application client ID
     * @param string $client_secret Discord application client secret
     * @param string $guild_id      Discord guild (server) ID
     */
    public function __construct(string $client_id, string $client_secret, string $guild_id) {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->guild_id = $guild_id;
    }

    /**
     * Get Guild Member
     * 
     * Retrieves information about a specific member in the guild.
     * 
     * @param string $user_id Discord user ID
     * @return array<string, mixed>|null Member data or null if not found
     */
    public function get_guild_member(string $user_id): ?array {
        $response = wp_remote_get(
            "https://discord.com/api/v10/guilds/{$this->guild_id}/members/{$user_id}",
            [
                'headers' => [
                    'Authorization' => "Bot {$this->access_token}",
                ]
            ]
        );

        if (is_wp_error($response)) {
            return null;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        return $body;
    }

    /**
     * Get Guild Roles
     * 
     * Retrieves all roles from the configured guild.
     * 
     * @return array<int, array<string, mixed>> Array of role objects
     */
    public function get_guild_roles(): array {
        $response = wp_remote_get(
            "https://discord.com/api/v10/guilds/{$this->guild_id}/roles",
            [
                'headers' => [
                    'Authorization' => "Bot {$this->access_token}",
                ]
            ]
        );

        if (is_wp_error($response)) {
            return [];
        }

        return json_decode(wp_remote_retrieve_body($response), true) ?? [];
    }

    /**
     * Exchange Authorization Code
     * 
     * Exchanges an OAuth2 authorization code for an access token.
     * 
     * @param string $code         Authorization code from Discord
     * @param string $redirect_uri OAuth2 redirect URI
     * @return array<string, mixed>|null Token response or null on failure
     */
    public function exchange_code(string $code, string $redirect_uri): ?array {
        $response = wp_remote_post('https://discord.com/api/v10/oauth2/token', [
            'body' => [
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $redirect_uri,
            ]
        ]);

        if (is_wp_error($response)) {
            return null;
        }

        return json_decode(wp_remote_retrieve_body($response), true);
    }

    /**
     * Get User Information
     * 
     * Retrieves information about the authenticated user.
     * 
     * @param string $access_token User's OAuth2 access token
     * @return array<string, mixed>|null User data or null on failure
     */
    public function get_user(string $access_token): ?array {
        $response = wp_remote_get('https://discord.com/api/v10/users/@me', [
            'headers' => [
                'Authorization' => "Bearer {$access_token}"
            ]
        ]);

        if (is_wp_error($response)) {
            return null;
        }

        return json_decode(wp_remote_retrieve_body($response), true);
    }

    /**
     * Get User's Discord Roles
     * 
     * Retrieves all roles for the authenticated user across their guilds.
     * 
     * @param string $user_id Discord user ID
     * @return array<int, string> Array of role IDs
     */
    public function get_user_roles(string $user_id): array {
        if (empty($this->access_token)) {
            return [];
        }

        $response = wp_remote_get('https://discord.com/api/v10/users/@me/guilds', [
            'headers' => [
                'Authorization' => "Bearer {$this->access_token}"
            ]
        ]);

        if (is_wp_error($response)) {
            return [];
        }

        $guilds = json_decode(wp_remote_retrieve_body($response), true);
        if (!is_array($guilds)) {
            return [];
        }

        $roles = [];
        foreach ($guilds as $guild) {
            if (isset($guild['roles'])) {
                $roles = array_merge($roles, $guild['roles']);
            }
        }

        return array_unique($roles);
    }
} 