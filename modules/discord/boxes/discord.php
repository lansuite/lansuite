<?php

/**
* Code to trigger Discord box generation and inclusion of the formatting CSS code
*/

$discord = new \LanSuite\Module\Discord\Discord();
$discordServerData = $discord->fetchServerData();
//Load either custom style definition or fall back to default one
if (file_exists('design/' . $auth['design'] . '/discord.css')) {
    $framework->add_css_path('design/' . $auth['design'] . '/discord.css');
} else {
    $framework->add_css_path('modules/discord/boxes/default.css');
}
if (!$discordServerData) {
    // Failed to fetch Discord status XML.
    // Possible reasons: No connectivity, Discord server issues, Widget not enabled in Discord server settings
    // TODO: Improve error reporting.
    $box->Row('<b>Error:</b> Unable to retrieve server data.');
} else {
    $boxcontent = $discord->genBoxContent($discordServerData);
    $box->Row($boxcontent);
}
