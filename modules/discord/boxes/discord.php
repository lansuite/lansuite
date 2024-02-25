<?php

$discord = new \LanSuite\Module\Discord\Discord();
$discordServerData = $discord->fetchServerData();

// Load either custom style definition or fall back to default one
$customCssPath = 'design/' . $auth['design'] . '/discord.css';
if (file_exists($customCssPath)) {
    $framework->add_css_path($customCssPath);

} else {
    $framework->add_css_path('modules/discord/boxes/default.css');
}

if (!$discordServerData) {
    // Failed to fetch Discord server data.
    // Possible reasons:
    //  - No connectivity
    //  - Discord server issues
    //  - Widget not enabled in Discord server settings
    // TODO: Improve error reporting.
    $box->Row('<b>Error:</b> Unable to retrieve server data.');

} else {
    $boxcontent = $discord->genBoxContent($discordServerData);
    $box->Row($boxcontent);
}
