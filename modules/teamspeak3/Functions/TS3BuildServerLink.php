<?php

/**
 * @param string $channel_ID
 * @param string $channel_password
 * @return string
 */
function TS3BuildServerLink($channel_ID = '', $channel_password = '')
{
    global $cfg;

    $link = 'ts3server://'. $cfg['ts3_serveraddress'];
    if (!empty($cfg['ts3_serverport'])) {
        $link .= '?port='. $cfg['ts3_serverport'];
    }
    if (!empty($cfg['ts3_serverpassword'])) {
        $link .= '?password='. $cfg['ts3_serverpassword'];
    }
    if (!empty($channel_ID)) {
        $link .= '?cid='. $channel_ID;
    }
    if (!empty($channel_password)) {
        $link .= '?channelpassword='. $channel_password;
    }

    return $link;
}
