<?php

namespace OrgManager\Features\Discord;

class DiscordClient {
    private string $client_id;
    private string $client_secret;
    private string $guild_id;
    private ?string $access_token = null;

    public function __construct(string $client_id, string $client_secret, string $guild_id) {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->guild_id = $guild_id;
    }

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
     * Get user's Discord roles
     * 
     * @param string $user_id Discord user ID
     * @return array Array of role IDs
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