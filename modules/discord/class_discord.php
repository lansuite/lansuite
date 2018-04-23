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
class class_discord {
    //put your code here
    
    
    //storage for the discord id
    private $discordId = 0;

    
    public function __construct($discordId=0){
        global $cfg,$func;
        //Check if  id was passed via constructor, use configuration value otherwise
        if ($discordId){
            $this->discordId = $disordId;
        } elseif (isset($cfg['discord_id'])) {
            $this ->discordId =  $cfg['discord_id'];
        } else {
            $func->error(t('Es wurde keine Discord server ID konfiguriert oder übergeben'));
        } 
    }
    
    /**
     * Retrieves JSON widget data from the Discord server via the public API
     * Data is being returned as multi-dimensional array
     * 
     * @return string[] Multi-dimensional array containing decoded JSON content, FALSE on error 
     */
    public function fetchServerData(){
        $APIurl = 'https://discordapp.com/api/guilds/'.$this->discordId .'/widget.json';
        $JsonReturnData = file_get_contents('$APIurl');
        return json_decode($JsonReturnData);
    }

    /**
     * Show discordbox
     *
     * @author CCG*Centurio
     * @version $Id: discord.php 1673 2018-04-04 08:13:47Z CCG*Centurio $
     * @return string Box content ready for output
     */
    public function genBox(){
        global $dsp;






        }
}

	$json = file_get_contents('https://discordapp.com/api/guilds/277788790565634048/widget.json');
	$discord = json_decode($json, true);
	echo "<li class='discord_server_name'>{$discord['name']}<br>";
	
	$discord1 = json_decode(file_get_contents('https://discordapp.com/api/servers/277788790565634048/widget.json'));


// -------------------------------- MEMBERS ---------------------------------------- //	

	
	// echo '<pre>' . print_r($discord, true) . '</pre>';
	
if (count($discord['members']) > 0) {
	echo '<span class="online_users badge green">' . count($discord['members']) . '</span>';
}
else {
	echo '<span class="online_users badge red">' . count($discord['members']) . '</span>';
} 
echo '<ul class="online_sidebar">';
	foreach($discord['members'] as $member):
		if (array_key_exists('nick', $member)) {
			echo '<li><img src="'. $member['avatar_url'] .'" class="'. $member['status'] .' discord_avatar">' . $member['nick'] . '</li>';
		}
		else {
			echo '<li><img src="'. $member['avatar_url'] .'" class="'. $member['status'] .' discord_avatar">' . $member['username'] . '</li>';
		}
	endforeach;
echo '</ul>';


// -------------------------------- CHANNELS ---------------------------------------- //


  if ($discord1->channels) {
	usort($discord1->channels, function($a, $b) {
	  return ($a->position > $b->position) ? 1 : -1;
	});
	echo '<ul class="online_sidebar_channel">';
	foreach ($discord1->members as $member) {
		if (array_key_exists('nick', $member) && !empty($member->channel_id)) {
			$channel_members[$member->channel_id][] = $member->nick;
		}
		elseif (!empty($member->channel_id)) {
			$channel_members[$member->channel_id][] = $member->username;
		}
	}
	foreach ($discord1->channels as $channel) {
	  echo "<li class='channel'>{$channel->name}";
	  if (!empty($channel_members[$channel->id])) {
			echo '<ul>';
			foreach ($channel_members[$channel->id] as $username) {
			  echo "<li class='channel_member'>$username</li>";
			}
			echo '</ul>';
	  }
	  echo "</li>";
	}
	echo "<input class=\"btn-join\" type=button onClick=\"parent.open('https://discordapp.com/invite/*********')\" value='Join'>";
	echo '</ul>';
  }
  
  
?> 
    
    
    
}
