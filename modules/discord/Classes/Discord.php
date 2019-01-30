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
    private $discordServerId = 0;
    

    
    public function __construct($discordServerId = 0){
        global $cfg,$func;
        
        //Have a look first, if OpenSSL is enabled as module...
       if (extension_loaded('openssl'))
       {
            //Check if server id was passed via constructor, use configuration value otherwise
            if ($discordServerId){
                $this->discordServerId = $disordServerId;
            } elseif (isset($cfg['discord_server_id'])) {
                $this ->discordServerId =  $cfg['discord_server_id'];
            } else {
                $func->error(t('Es wurde keine Discord server ID konfiguriert oder übergeben'));
            } 
       }
        else {
            $func->error('OpenSSL-Modul nicht geladen!');
        }
    }
    
    /**
     * Retrieves JSON widget data from the Discord server via the public API
     * Data is being returned as multi-dimensional array
     * 
     * @return stdClass decoded JSON content as object of stdClass, FALSE on error 
     */
    
    public function fetchServerData(){
        $APIurl = 'https://discordapp.com/api/servers/'.$this->discordServerId .'/widget.json';
        $JsonReturnData = @file_get_contents($APIurl);
        return ($JsonReturnData===false ? false : json_decode($JsonReturnData, false));
    }

    /**
     * Show discordbox
     *
     * @author CCG*Centurio
     * @version $Id: discord.php 1673 2018-04-04 08:13:47Z CCG*Centurio $
     * @return string Box content ready for output
     */
    public function genBoxContent($discordServerData){
        $boxContent ="<li class='discord_server_name'>{$discordServerData->name} ";
        // -------------------------------- MEMBERS ---------------------------------------- // 
        if (count($discordServerData->members) > 0) {
            $boxContent .= '<span class="online_users badge green">' . count($discordServerData->members) . '</span>';
        }
        else {
            $boxContent .= '<span class="online_users badge red">' . count($discordServerData->members) . '</span>';
        } 
        $boxContent .= '<ul class="online_sidebar">';
        foreach($discordServerData->members as $member){
            if (array_key_exists('nick', $member)) {
                $boxContent .= '<li><img src="'. $member->avatar_url .'" class="'. $member->status .' discord_avatar">' . $member->nick . '</li>';
            }
            else {
                $boxContent .= '<li><img src="'. $member->avatar_url .'" class="'. $member->status .' discord_avatar">' . $member->username . '</li>';
            }
        }
        $boxContent .= '</ul>';
        // -------------------------------- CHANNELS ---------------------------------------- //
        if ($discordServerData->channels) {
            usort($discordServerData->channels, function($a, $b) {
            return ($a->position > $b->position) ? 1 : -1;
                    });
            $boxContent .= '<ul class="online_sidebar_channel">';
            foreach ($discordServerData->members as $member) {
                if (array_key_exists('nick', $member) && !empty($member->channel_id)) {
                    $channel_members[$member->channel_id][] = $member->nick;
                }
                elseif (!empty($member->channel_id)) {
                    $channel_members[$member->channel_id][] = $member->username;
                }
            }
            foreach ($discordServerData->channels as $channel) {
                $boxContent .= "<li class='channel'>{$channel->name}";
                if (!empty($channel_members[$channel->id])) {
                    $boxContent .= '<ul>';
                    foreach ($channel_members[$channel->id] as $username) {
                        $boxContent .= "<li class='channel_member'>$username</li>";
                    }
                    $boxContent .= '</ul>';
                }
                $boxContent .= "</li>";
            }  
        }
        if (!is_null($discordServerData->instant_invite)) {
            $boxContent .= "<input class=\"btn-join\" type=button onClick=\"parent.open('". $discordServerData->instant_invite ." ')\" value='Join'>";
        }
        $boxContent .= '</li>';
        return $boxContent;
    }
}
