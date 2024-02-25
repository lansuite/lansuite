<?php

// Determine Discord guild ID
$discordServerID = $cfg['discord_server_id'] ?? '';
if (!$discordServerID) {
    $box->Row('<b>Error:</b> Es wurde keine Discord Server ID konfiguriert.');
    return;
}

$discord = new \LanSuite\Module\Discord\Discord($discordServerID, $cache, $httpClient);
$discordServerData = $discord->fetchServerData();

// Failed to fetch Discord server data.
if ($discord->containsServerError($discordServerData)) {
    $errorMessage = sprintf('%s (%s)', $discordServerData['message'], $discordServerData['code']);
    $box->Row('<b>Error:</b> ' . $errorMessage);
    return;
}

// Load either custom style definition or fall back to default one
$customCssPath = 'design/' . $auth['design'] . '/discord.css';
if (file_exists($customCssPath)) {
    $framework->add_css_path($customCssPath);

} else {
    $framework->add_css_path('modules/discord/boxes/default.css');
}

$boxcontent = $discord->genBoxContent($discordServerData);
$box->Row($boxcontent);
