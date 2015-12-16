<?php

/*
* Splitting of TS3 code into a separate class...
 */

/**
 * Description of class_teamspeak3
 *
 * @author MaLuZ
 */
class Teamspeak3Server {
    //put your code here

    private $TS3;
    private $settings;
    private $connected = false;
    
    //default constructor. Either uses the default config..or not
    public function __construct ($LoadStandardConfig=true) {    
        //If nothing is passed at construction time, we use whatever we find in the configuration...
        If ($LoadStandardConfig){ $this->LoadLanSuiteConfig();};    
    }
    
    //function to get the default configuration from LanSuite
    private function LoadLanSuiteConfig(){
        global $cfg;
        $this->settings["serveraddress"] = $cfg['ts3_serveraddress'];
        $this->settings["serverpassword"] = $cfg['ts3_serverpassword'];
        $this->settings["serverudpport"] = $cfg['ts3_serverudpport'];
        $this->settings["serverqueryport"] = $cfg['ts3_serverqueryport'];
        $this->settings["serverqueryuser"] = $cfg['ts3_serverqueryuser'];
        $this->settings["serverquerypassword"] = $cfg['ts3_serverquerypassword'];
        //$this->settings["tournamentchannel"] = $cfg['ts3_tournamentchannel'];  
    }
    
    //public constructor if connection should be to a non-standard server
    public static function CreateObj($serveraddress, $serverudpport ,$serverpassword, $serverqueryport, $serverqueryuser, $serverquerypassword){
        $Obj = new Teamspeak3Server(false);
        $Obj->settings["serveraddress"] = $serveraddress;
        $Obj->settings["serverpassword"] = $serverpassword;
        $Obj->settings["serverudpport"] = $serverudpport;
        $Obj->settings["serverqueryport"] = $serverqueryport;
        $Obj->settings["serverqueryuser"] = $serverqueryuser;
        $Obj->settings["serverquerypassword"] = $serverquerypassword;
        //$Obj->settings["tournamentchannel"] = $cfg['ts3_tournamentchannel'];  
        return $Obj;
    }
    
    //returns a URL for the TS server
    public function GetURL ($ChannelId = '', $ChannelPW = '', $SetUserName = false){
        $link = 'ts3server://'. $this->settings['serveraddress'];
        if (!empty($this->settings['serverport'])) $link .= '?port='. $cfg['ts3_serverport'];
        if (!empty($this->settings['ts3_serverpassword'])) $link .= '?password='. $cfg['ts3_serverpassword'];
        if (!empty($ChannelId)) $link .= '?cid='. $channel_ID;
        if (!empty($ChannelPW)) $link .= '?channelpassword='. $channel_password;
        //if ($SetUserName) $link .= '?username='. $channel_password;
        return $link;
    }
    
    public function connect(){


    }
    
    public function disconnect(){
        
        
    }


}
