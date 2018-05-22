<?php

namespace LanSuite\Modules\TeamSpeak3;

class TeamSpeak3 {
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
        if (!empty($cfg['ts3_tournamentchannel']))$this->settings["tournamentchannel"] = $cfg['ts3_tournamentchannel'];  
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
        global $func;
        try {
        //create object
        $this->TS3 = TeamSpeak3::factory("serverquery://" . /*$settings['serverqueryuser'].':'.$settings['serverquerypassword'].'@'.*/ $this->settings['ts3_serveraddress']. ':' . $this->settings['ts3_serverqueryport']);
        $this->TS3->serverSelectById(1); //select VirtualServer 1      
        }
        catch (TeamSpeak3_Exception $e){
            //@TODO: Add proper Error-Handling
            $func->error("Konnte nicht zum TS-Server verbinden. Fehlermeldung:\n" . $e->getMessage());
        }
    }
    
    public function disconnect(){
        unlink($this->TS3);
        
    }
    public function ViewServerOverview(){
        return $this->TS3->getViewer(new TeamSpeak3_Viewer_Html("ext_inc/teamspeak3/images/viewer/", "ext_inc/teamspeak3/images/countryflags/", "data:image")); //generate output
        //@TODO: Create custom, LS-Specific code
        
    }
    /**
     * Add a lobby for a Match between two teams. 
     * Automatically creates the subchannels
     * @param int $MatchID1 Match ID for the first team
     * @param int $MatchID2 Match ID for the second team
     */
    private function AddMatchLobby($MatchID1, $MatchID2){
        
    }
    /**
     * Create a channel that serves as Lobby for the Match 
     * @param int $TournamentID The ID of the Tournament we should create this for
     */
    private function AddTournamentChannel($TournamentID){
        global $func;
      $password = rand(10000, 99999);
      try{
          $this->TS3->channelCreate(
                  array(
                      "channel_name" => "My Sub-Channel",
                      "channel_topic" => "This is a sub-level channel",
                      "channel_codec" => TeamSpeak3::CODEC_OPUS_VOICE,
                      "channel_flag_permanent" => TRUE,
                      "channel_password" => $password,
                      "cpid" => $this->settings["tournamentchannel"])
                  );
          //@TODO: Add data as comment (or separate Field) to Match
      } catch (TeamSpeak3_Exception $e) {
              $func->Error("Konnte Channel nicht erzeugen. Fehlermeldung: \n". $e->getMessage());
      }  
        
    }
    
    private function GetChannelID($ChannelName){
        
        
    }
    /**
     * Returns true if the given ChannelID exists, false otherwise
     * @param int $ChannelID
     */
    private function ChannelExists($ChannelID){
        
        
    }
    
    /**
     * Creates a channel 
     * @global type $func
     * @param type $MatchID
     * @param type $ParentChannel
     */
    public function AddTeamMatchChannel($MatchID, $ParentChannel){
        global $func;
            $password = rand(10000, 99999);
            try{
                $this->TS3->channelCreate(
                        array(
                            "channel_name" => "My Sub-Channel",
                            "channel_topic" => "This is a sub-level channel",
                            "channel_codec" => TeamSpeak3::CODEC_OPUS_VOICE,
                            "channel_flag_permanent" => TRUE,
                            "channel_password" => $password,
                            "cpid" => $this->settings["tournamentchannel"])
                        );
                //@TODO: Add data as comment (or separate Field) to Match
            } catch (TeamSpeak3_Exception $e) {
                    $func->Error("Konnte Channel nicht erzeugen. Fehlermeldung: \n". $e->getMessage());
            }
            }
    public function DelTournamentChannel($MatchID){
        //@TODO: Get previous channel data from Match
        //$this->TS3->channelDelete()
    }
}
