<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace LanSuite\Module\Discord;
/**
 * Class implementation for all functions required to interact with the Discord and LS API
 * General intention is to roll this up with the TS3 code into a general "voiceserver" parent class
 *
 * @author MaLuZ
 */
class Discord {

    // Storage for the discord server id
    private $discordServerId = '';
    

    
    public function __construct($discordServerId = ''){
        global $cfg,$func;
        
        if (!extension_loaded('openssl')) {
            $func->error('OpenSSL-Modul nicht geladen!');
        } else if (!ini_get('allow_url_fopen')) {
            $func->error('allow_url_fopen nicht aktiv');
        } else {
            //Check if server id was passed via constructor, use configuration value otherwise
            if ($discordServerId!=''){
                $this->discordServerId = $disordServerId;
            } elseif (isset($cfg['discord_server_id'])) {
                $this ->discordServerId =  $cfg['discord_server_id'];
            } else {
                $func->error(t('Es wurde keine Discord Server ID konfiguriert oder übergeben'));
            } 
        }
    }
    
    /**
     * Retrieves JSON widget data from the Discord server via the public API
     * Data is being returned as multi-dimensional array
     * 
     * @return stdClass decoded JSON content as object of stdClass, FALSE on error 
     */
    
    public function fetchServerData(){
        global $cfg;

        clearstatcache('ext_inc/discord/cache.json');
        if (is_readable('ext_inc/discord/cache.json') && time()-filemtime('ext_inc/discord/cache.json') < 60) {
            // Cache file is readable and <60 seconds old.
            // Note: Discord itself currently seems to update the widget.json file only once every 300 seconds.
            $JsonReturnData = file_get_contents('ext_inc/discord/cache.json');
        } else if (is_readable('ext_inc/discord/cache.json') && filesize('ext_inc/discord/cache.json') < 2 && time()-filemtime('ext_inc/discord/cache.json') < 300) {
            // Cache file exists but is too small. There was probably some issue retrieving Discord data.
            // Only retry after 300 seconds.
            return false;
        } else {
            // No cache file or too old; let's fetch data.
            $APIurl = 'https://discordapp.com/api/servers/'.$this->discordServerId .'/widget.json';
            $JsonReturnData = @file_get_contents($APIurl,false,stream_context_create(array('http' => array('timeout' => (isset($cfg['discord_json_timeout']) ? $cfg['discord_json_timeout'] : 4)))));
            if (is_writeable('ext_inc/discord/')) {
                // Note: This intentionally also writes empty results to the cache file.
                @file_put_contents('ext_inc/discord/cache.json', $JsonReturnData, LOCK_EX);
            }
        }
        return ($JsonReturnData === false ? false : json_decode($JsonReturnData, false));
    }

    /**
     * Show discordbox
     *
     * @author CCG*Centurio
     * @version $Id: discord.php 1673 2018-04-04 08:13:47Z CCG*Centurio $
     * @return string Box content ready for output
     */
    public function genBoxContent($discordServerData){
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
        }
        else {
            $onlinemembers = count($discordServerData->members);
        }
        if ($onlinemembers > 0) {
            $boxContent .= '<span class="online_users badge green">' . $onlinemembers . '</span>';
        }
        else {
            $boxContent .= '<span class="online_users badge red">0</span>';
        } 
        if (isset($cfg['discord_show_global_members']) && $cfg['discord_show_global_members'] == 1) {
            $boxContent .= '<ul class="online_sidebar">';
            foreach($discordServerData->members as $member){
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
                usort($discordServerData->channels, function($a, $b) {
                return ($a->position > $b->position) ? 1 : -1;
                        });
                $boxContent .= '<ul class="online_sidebar_channel">';
                foreach ($discordServerData->members as $member) {
                    if (isset($cfg['discord_hide_bots']) && $cfg['discord_hide_bots'] == 1 && $member->bot) {
                        continue;
                    }
                    if (array_key_exists('nick', $member) && !empty($member->channel_id)) {
                        $channel_members[$member->channel_id][] = $member->nick;
                    }
                    elseif (!empty($member->channel_id)) {
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
