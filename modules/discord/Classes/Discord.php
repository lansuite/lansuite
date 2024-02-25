<?php

namespace LanSuite\Module\Discord;

/**
 * Contains all functions required to interact with the Discord and LanSuite API.
 */
class Discord {

    /**
     * Discord Guild/Server ID
     */
    private string $discordServerId = '';
    
    public function __construct($discordServerId)
    {
        $this->discordServerId = $discordServerId;
    }
    
    /**
     * Retrieves JSON widget data from the Discord server via the public API
     * Data is being returned as multi-dimensional array
     *
     * @return stdClass decoded JSON content as object of stdClass, FALSE on error
     */
    
    public function fetchServerData()
    {
        global $cfg, $cache;

        $discordCache = $cache->getItem('discord.cache');
        if (!$discordCache->isHit()) {
            // No cache file or too old; let's fetch data.
            $APIurl = 'https://discordapp.com/api/servers/'.$this->discordServerId .'/widget.json';
            $JsonReturnData = @file_get_contents($APIurl, false, stream_context_create(array('http' => array('timeout' => ($cfg['discord_json_timeout'] ?? 4)))));

            // Store in cache with timeout of 60 seconds
            $discordCache->set($JsonReturnData, 60);
            $cache->save($discordCache);
        }
        $JsonReturnData = $discordCache->get();

        return ($JsonReturnData === false ? false : json_decode($JsonReturnData, false));
    }

    /**
     * Show discordbox
     *
     * @author CCG*Centurio
     * @version $Id: discord.php 1673 2018-04-04 08:13:47Z CCG*Centurio $
     * @return string Box content ready for output
     */
    public function genBoxContent($discordServerData)
    {
        global $cfg;

        $boxContent ="<li class='discord_server_name'>{$discordServerData->name} ";
        // -------------------------------- MEMBERS ---------------------------------------- //
        if (isset($cfg['discord_hide_bots']) && $cfg['discord_hide_bots'] == 1) {
            $onlinemembers = 0;
            foreach ($discordServerData->members as $member) {
                if (!$member->bot) {
                    $onlinemembers++;
                }
            }
        } else {
            $onlinemembers = count($discordServerData->members);
        }
        if ($onlinemembers > 0) {
            $boxContent .= '<span class="online_users badge green">' . $onlinemembers . '</span>';
        } else {
            $boxContent .= '<span class="online_users badge red">0</span>';
        }
        if (isset($cfg['discord_show_global_members']) && $cfg['discord_show_global_members'] == 1) {
            $boxContent .= '<ul class="online_sidebar">';
            foreach ($discordServerData->members as $member) {
                if (isset($cfg['discord_hide_bots']) && $cfg['discord_hide_bots'] == 1 && $member->bot) {
                    continue;
                }
                $username = $member->username;
                if (array_key_exists('nick', $member)) {
                    $username = $member->nick;
                }
                if (isset($cfg['discord_max_user_length']) && strlen($username) > $cfg['discord_max_user_length']) {
                    $username = substr($username, 0, $cfg['discord_max_user_length'] - 2).'..';
                }
                $boxContent .= '<li><img src="'. $member->avatar_url .'" class="'. $member->status .' discord_avatar">' . $username . '</li>';
            }
            $boxContent .= '</ul>';
        }
        // -------------------------------- CHANNELS ---------------------------------------- //
        if (isset($cfg['discord_show_channels']) && $cfg['discord_show_channels'] == 1) {
            if ($discordServerData->channels) {
                usort($discordServerData->channels, fn($a, $b) => ($a->position > $b->position) ? 1 : -1);
                $boxContent .= '<ul class="online_sidebar_channel">';
                foreach ($discordServerData->members as $member) {
                    if (isset($cfg['discord_hide_bots']) && $cfg['discord_hide_bots'] == 1 && $member->bot) {
                        continue;
                    }
                    if (array_key_exists('nick', $member) && !empty($member->channel_id)) {
                        $channel_members[$member->channel_id][] = $member->nick;
                    } elseif (!empty($member->channel_id)) {
                        $channel_members[$member->channel_id][] = $member->username;
                    }
                }
                foreach ($discordServerData->channels as $channel) {
                    if (isset($cfg['discord_hide_empty_channels']) && $cfg['discord_hide_empty_channels'] == 1 && empty($channel_members[$channel->id])) {
                        continue;
                    }
                    $channelname = $channel->name;
                    if (isset($cfg['discord_max_channel_length']) && strlen($channelname) > $cfg['discord_max_channel_length']) {
                        $channelname = substr($channelname, 0, $cfg['discord_max_channel_length'] - 2).'..';
                    }
                    $boxContent .= "<li class='channel'>{$channelname}";
                    if (!empty($channel_members[$channel->id])) {
                        $boxContent .= '<ul>';
                        foreach ($channel_members[$channel->id] as $username) {
                            if (isset($cfg['discord_max_user_length']) && strlen($username) > $cfg['discord_max_user_length']) {
                                $username = substr($username, 0, $cfg['discord_max_user_length'] - 2).'..';
                            }
                            $boxContent .= "<li class='channel_member'>$username</li>";
                        }
                        $boxContent .= '</ul>';
                    }
                    $boxContent .= "</li>";
                }
            }
        }
        if (!is_null($discordServerData->instant_invite) && isset($cfg['discord_show_join_button']) && $cfg['discord_show_join_button'] == 1) {
            $boxContent .= "<input class=\"btn-join\" type=button onClick=\"parent.open('". $discordServerData->instant_invite ." ')\" value='Join'>";
        }
        $boxContent .= '</li>';
        return $boxContent;
    }
}
