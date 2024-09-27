<?php

namespace LanSuite\Module\Discord;

/**
 * Contains all functions required to interact with the Discord and LanSuite API.
 */
class Discord {

    /**
     * API URL of Discord widget.
     *
     * @link https://discord.com/developers/docs/resources/guild#get-guild-widget
     */
    private const DISCORD_WIDGET_URL = 'https://discord.com/api/guilds/%s/widget.json';

    /**
     * Discord Guild/Server ID
     */
    private string $discordServerId = '';

    /**
     * HTTP Client to call external APIs.
     */
    private readonly \Symfony\Contracts\HttpClient\HttpClientInterface $httpClient;

    /**
     * Cache
     */
    private readonly \Symfony\Contracts\Cache\CacheInterface $cache;

    /**
     * Cache TTL of the discord response in seconds.
     */
    private int $cacheTTL = 60;

    /**
     * Request API timeout in seconds.
     */
    private int $discordAPITimeout = 4;
    
    public function __construct(
        $discordServerId,
        \Symfony\Contracts\Cache\CacheInterface $cache,
        \Symfony\Contracts\HttpClient\HttpClientInterface $httpClient
    ) {
        global $cfg;

        $this->discordServerId = $discordServerId;
        $this->cache = $cache;
        $this->httpClient = $httpClient;
        $this->discordAPITimeout = ($cfg['discord_json_timeout'] ?? $this->discordAPITimeout);
    }
    
    /**
     * Retrieves the Discord JSON widget via the public API.
     *
     * @return stdClass decoded JSON content as object of stdClass, FALSE on error
     */
    public function fetchServerData(): array
    {
        $discordCache = $this->cache->getItem('discord.cache');
        if ($discordCache->isHit()) {
            $cacheContent = $discordCache->get();
            $content = json_decode($discordCache->get(), true, 512, \JSON_BIGINT_AS_STRING);
            return $content;
        }

        // No cache file or too old; let's fetch data.
        $apiURL = sprintf(self::DISCORD_WIDGET_URL, $this->discordServerId);
        $response = $this->httpClient->request(
            'GET',
            $apiURL,
            ['timeout' => $this->discordAPITimeout]
        );

        try {
            $apiContent = $response->toArray(false);
        } catch(\Symfony\Component\HttpClient\Exception\TransportException $e) {
            // E.g. If network connection is not given
            $apiContent = [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ];
        }

        // Check if we have an error
        // A successful response doesn't contain code and message
        //
        // An unsuccessful response can contain different error types:
        //  - {"message": "Unbekannter Server", "code": 10004}
        //  - {"message": "Widget deaktiviert", "code": 50004}
        //
        // @link https://discord.com/developers/docs/topics/opcodes-and-status-codes#json-json-error-codes
        if ($this->containsServerError($apiContent)) {
            return $apiContent;
        }

        // Store in cache
        $discordCache->set($response->getContent(false), $this->cacheTTL);
        $this->cache->save($discordCache);

        return $apiContent;
    }

    /**
     * Checks if the Discord Server API response contains an error.
     */
    public function containsServerError(array $apiResponse): bool {
        if (array_key_exists('code', $apiResponse) && array_key_exists('message', $apiResponse)) {
            return true;
        }

        return false;
    }

    /**
     * Show box with Discord data
     */
    public function generateBoxContent(array $discordServerData): string
    {
        global $cfg;

        $boxContent = "<li class='discord_server_name'>{$discordServerData['name']} ";

        // Members
        if (array_key_exists('discord_hide_bots', $cfg) && $cfg['discord_hide_bots'] == 1) {
            $onlinemembers = 0;
            foreach ($discordServerData['members'] as $member) {
                if (!array_key_exists('bot', $member) || !$member['bot']) {
                    $onlinemembers++;
                }
            }
        } else {
            $onlinemembers = count($discordServerData['members']);
        }

        if ($onlinemembers > 0) {
            $boxContent .= '<span class="online_users badge green">' . $onlinemembers . '</span>';
        } else {
            $boxContent .= '<span class="online_users badge red">0</span>';
        }

        if (array_key_exists('discord_show_global_members', $cfg) && $cfg['discord_show_global_members'] == 1) {
            $boxContent .= '<ul class="online_sidebar">';
            foreach ($discordServerData['members'] as $member) {
                if (array_key_exists('discord_hide_bots', $cfg) && $cfg['discord_hide_bots'] == 1 && (array_key_exists('bot', $member) && $member['bot'])) {
                    continue;
                }

                $username = $member['username'];
                if (array_key_exists('discord_max_user_length', $cfg) && strlen($username) > $cfg['discord_max_user_length']) {
                    $username = substr($username, 0, $cfg['discord_max_user_length'] - 2) . '..';
                }
                $boxContent .= '<li><img src="' . $member['avatar_url'] . '" class="' . $member['status'] . ' discord_avatar">' . $username . '</li>';
            }
            $boxContent .= '</ul>';
        }

        // Channels
        if (array_key_exists('discord_show_channels', $cfg) && $cfg['discord_show_channels'] == 1) {
            if (count($discordServerData['channels'])) {
                usort($discordServerData['channels'], fn($a, $b) => ($a['position'] > $b['position']) ? 1 : -1);
                $boxContent .= '<ul class="online_sidebar_channel">';
                foreach ($discordServerData['members'] as $member) {
                    if (array_key_exists('discord_hide_bots', $cfg) && $cfg['discord_hide_bots'] == 1 && (array_key_exists('bot', $member) && $member['bot'])) {
                        continue;
                    }
                    if (!empty($member['channel_id'])) {
                        $channel_members[$member['channel_id']][] = $member['username'];
                    }
                }
                foreach ($discordServerData['channels'] as $channel) {
                    if (array_key_exists('discord_hide_empty_channels', $cfg) && $cfg['discord_hide_empty_channels'] == 1 && empty($channel_members[$channel['id']])) {
                        continue;
                    }
                    $channelname = $channel['name'];
                    if (array_key_exists('discord_max_channel_length', $cfg) && strlen($channelname) > $cfg['discord_max_channel_length']) {
                        $channelname = substr($channelname, 0, $cfg['discord_max_channel_length'] - 2) . '..';
                    }
                    $boxContent .= "<li class='channel'>{$channelname}";
                    if (!empty($channel_members[$channel['id']])) {
                        $boxContent .= '<ul>';
                        foreach ($channel_members[$channel['id']] as $username) {
                            if (array_key_exists('discord_max_user_length', $cfg) && strlen($username) > $cfg['discord_max_user_length']) {
                                $username = substr($username, 0, $cfg['discord_max_user_length'] - 2) . '..';
                            }
                            $boxContent .= "<li class='channel_member'>$username</li>";
                        }
                        $boxContent .= '</ul>';
                    }
                    $boxContent .= "</li>";
                }
            }
        }

        if (!is_null($discordServerData['instant_invite']) && array_key_exists('discord_show_join_button', $cfg) && $cfg['discord_show_join_button'] == 1) {
            $boxContent .= "<input class=\"btn-join\" type=button onClick=\"parent.open('" . $discordServerData['instant_invite'] . "')\" value='Join'>";
        }
        $boxContent .= '</li>';

        return $boxContent;
    }
}
