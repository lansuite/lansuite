<?php

namespace LanSuite\Modules\TeamSpeak3;

class TeamSpeak3
{

    /**
     * @var \TeamSpeak3_Node_Abstract
     */
    private $teamspeak;

    /**
     * @var array
     */
    private $settings = [];

    public function __construct($LoadStandardConfig = true)
    {
        if ($LoadStandardConfig) {
            $this->loadLanSuiteConfig();
        }
    }

    /**
     * Loads the default configuration LanSuite
     * @return void
     */
    private function loadLanSuiteConfig()
    {
        global $cfg;

        $this->settings["serveraddress"]        = $cfg['ts3_serveraddress'];
        $this->settings["serverpassword"]       = $cfg['ts3_serverpassword'];
        $this->settings["serverudpport"]        = $cfg['ts3_serverudpport'];
        $this->settings["serverqueryport"]      = $cfg['ts3_serverqueryport'];
        $this->settings["serverqueryuser"]      = $cfg['ts3_serverqueryuser'];
        $this->settings["serverquerypassword"]  = $cfg['ts3_serverquerypassword'];
        if (!empty($cfg['ts3_tournamentchannel'])) {
            $this->settings["tournamentchannel"] = $cfg['ts3_tournamentchannel'];
        }
    }

    /**
     * Returns a URL for the TS server
     *
     * @param string $channelID
     * @param string $channelPassword
     * @return string
     */
    public function getURL($channelID = '', $channelPassword = '')
    {
        global $cfg;

        $link = 'ts3server://'. $this->settings['serveraddress'];
        if (!empty($this->settings['serverport'])) {
            $link .= '?port='. $cfg['ts3_serverport'];
        }
        if (!empty($this->settings['ts3_serverpassword'])) {
            $link .= '?password='. $cfg['ts3_serverpassword'];
        }
        if (!empty($channelID)) {
            $link .= '?cid='. $channelID;
        }
        if (!empty($channelPassword)) {
            $link .= '?channelpassword='. $channelPassword;
        }

        return $link;
    }

    /**
     * @return void
     */
    public function connect()
    {
        global $func;
        try {
            $this->teamspeak = \TeamSpeak3::factory("serverquery://" . /*$settings['serverqueryuser'].':'.$settings['serverquerypassword'].'@'.*/ $this->settings['ts3_serveraddress']. ':' . $this->settings['ts3_serverqueryport']);
            $this->teamspeak->serverSelectById(1);
        } catch (TeamSpeak3_Exception $e) {
            // TODO: Add proper Error-Handling
            $func->error("Konnte nicht zum TS-Server verbinden. Fehlermeldung:\n" . $e->getMessage());
        }
    }

    /**
     * @return void
     */
    public function disconnect()
    {
        $this->teamspeak = null;
    }

    /**
     * @return string
     */
    public function viewServerOverview()
    {
        $content = $this->teamspeak->getViewer(new TeamSpeak3_Viewer_Html("vendor/planetteamspeak/ts3-php-framework/images/viewer", "vendor/planetteamspeak/ts3-php-framework/images/flags", "data:image"));
        return $content;
    }

    /**
     * Create a channel that serves as Lobby for the Match
     *
     * @param int $TournamentID The ID of the Tournament we should create this for
     * @return void
     */
    private function addTournamentChannel($TournamentID)
    {
        global $func;

        $password = rand(10000, 99999);
        try {
            $this->teamspeak->channelCreate([
            'channel_name'           => 'My Sub-Channel',
            'channel_topic'          => 'This is a sub-level channel',
            'channel_codec'          => \TeamSpeak3::CODEC_OPUS_VOICE,
            'channel_flag_permanent' => true,
            'channel_password'       => $password,
            'cpid'                   => $this->settings['tournamentchannel']
            ]);

        // TODO: Add data as comment (or separate Field) to Match
        } catch (\Exception $e) {
            $func->Error("Konnte Channel nicht erzeugen. Fehlermeldung: \n". $e->getMessage());
        }
    }
}
