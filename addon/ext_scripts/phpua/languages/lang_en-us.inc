<?php

    
    startBenchmark(__FILE__, __FUNCTION__);
    
    $_LANG["en-us"][0] = "English (United States)";
    addCredits("Translation: en-us", "Kris Splittgerber");
    
    // install.php
    $_LANG["en-us"]["ERROR"] = "Fehler";
    $_LANG["en-us"]["WARNING"] = "Warnung";
    $_LANG["en-us"]["READTHISPAGE"] = "Lese diese Seite";
    $_LANG["en-us"]["Installation"] = "Installation"; // Context: (title) phpUA Installation
    $_LANG["en-us"]["Finish"] = "Fertig";
    $_LANG["en-us"]["IAgree"] = "Ich Akzeptiere";
    $_LANG["en-us"]["Next"] = "Nächste";
    $_LANG["en-us"]["Yes"] = "Ja";
    $_LANG["en-us"]["No"] = "Nein";
    $_LANG["en-us"]["Unknown"] = "Unwissend";
    $_LANG["en-us"]["Overview"] = "Überblick";
    $_LANG["en-us"]["LicenseAgreement"] = "License Vereinbarung";
    $_LANG["en-us"]["SystemRequirements"] = "System Voraussetzung";
    $_LANG["en-us"]["InstallationComplete"] = "Installation Komplett";
    $_LANG["en-us"]["Step"] = "Stufe";
    $_LANG["en-us"]["Package"] = "Packet";
    $_LANG["en-us"]["Distributor"] = "Distributor";
    $_LANG["en-us"]["MSG_FINALWORDS"] = "<p>Now that you have installed phpUA, it cannot be installed again without uploading the <em>install.php</em> file and the <em>/install</em> directory (unless it hasn't been automatically removed, shown by a warning message above).</p><p>Please take the time to read through the <a href=\"doc/index.html\" target=\"_blank\">Documentation</a> in its entirety. If you have problems using phpUA after reading through the documentation, then you may ask for support at the <a href=\"http://www.unitedadmins.com/\">United Admins</a> forums.</p><p>We appreciate any and all feedback. If you find phpUA useful then please let us know at the <a href=\"http://www.unitedadmins.com/\">United Admins</a> forums.</p><p>Installation has now been completed. Click the &quot;Finish&quot; button to begin using phpUA.</p>";
    $_LANG["en-us"]["MSG_CREATEADMIN"] = "You must create at least one administrative account to customise your phpUA installation further. Your password will be hidden as you enter it so you must retype it to confirm that it is without typos. Your password will be stored on your web server using the <a href=\"http://www.faqs.org/rfcs/rfc1321\" target=\"_blank\">RSA Data Security, Inc. MD5 Message-Digest Algorithm</a>. You will be able to change the password and add extra administrative accounts after installation.";
    $_LANG["en-us"]["MSG_FILEDIRPERM"] = "File/Directory Permissions:";
    $_LANG["en-us"]["MSG_LATESTPHPUA"] = "Letzte Verion:";
    $_LANG["en-us"]["MSG_AGREEORDISAGREE"] = "If you agree to this License Agreement, click the I Agree button.<br>If you DO NOT agree to this License Agreement, close your browser now.";
    $_LANG["en-us"]["MSG_PACKAGEDESC"] = "Thank you for taking an interest in phpUA. phpUA is a PHP script that enables gaming server administrators to manage their servers via a website interface and to display server information including live statistics to the public. Plug-ins allow for game, language and website design compatibility.";
    $_LANG["en-us"]["MSG_CONTINUE"] = "Click the Next button, to continue with installation.";
    $_LANG["en-us"]["ERROR_OLDPHPUA"] = "For security reasons, you should only install the latest version of phpUA. You may download the latest version of phpUA from United Admins:";
    $_LANG["en-us"]["ERROR_UACONTACTFAILED"] = "The installer was unable to determine the latest version of phpUA.";
    $_LANG["en-us"]["ERROR_MOD_GZIP"] = "Your web server is running the 'mod_gzip' module. Ensure that 'mod_gzip' is disabled for phpUA as it may cause unexpected behavior.";
    $_LANG["en-us"]["ERROR_SAFEMODE"] = "Your web server is currently running in PHP's Safe Mode. This will limit your viewing time on live streams to"; // Context: ...viewing time on live streams to 30 sec.
    $_LANG["en-us"]["ERROR_FILEPERM"] = "does not have permission to write the file:"; // Context: "install.php does not have permission to write the file phpua.cfg.php"
    $_LANG["en-us"]["ERROR_DIRPERM"] = "does not have permission to write files to the directory:";
    $_LANG["en-us"]["ERROR_REMOVEFAILED"] = "Unable to remove the file: install.php. You MUST remove this manually!";
    
    // index.php
    $_LANG["en-us"]["phpUAEngine"] = "LS2 PHPUA Engine";
    $_LANG["en-us"]["ReturntoServerIndex"] = "Zurück zum Server Index";
    $_LANG["en-us"]["poweredby"] = "powered by";
    $_LANG["en-us"]["credits"] = "credits";
    $_LANG["en-us"]["SpecialThanks"] = "Special Thanks";
    $_LANG["en-us"]["Error"] = "Fehler";
    $_LANG["en-us"]["ERROR_ACCESSDENIED"] = "Zugriff Verweigert";
    $_LANG["en-us"]["ERROR_DIRECTORYERROR"] = "Datei oder Verzeichniss nicht gefunden: "; // context: "Unable to locate or open the directory: languages"
    $_LANG["en-us"]["ERROR_FILEERROR"] = "Kann Datei nicht finden oder öffenen: "; // context: "Unable to locate or open the file: index.php"
    
    // includes/content/credits.inc
    $_LANG["en-us"]["Credits"] = "Credits";
    
    // includes/content/index.inc
    $_LANG["en-us"]["ServerIndex"] = "Server Index";
    $_LANG["en-us"]["Administration"] = "Administration";
    $_LANG["en-us"]["ServerOnline"] = "Server Online";
    $_LANG["en-us"]["ServerOffline"] = "Server Offline";
    $_LANG["en-us"]["on"] = "on"; // Context: "There are 12 players 'on' de_dust"
    $_LANG["en-us"]["Login"] = "Login";
    $_LANG["en-us"]["Loggedinas"] = "Logged in as";
    $_LANG["en-us"]["Logout"] = "Logout";
    $_LANG["en-us"]["Help"] = "Hilfe";
    $_LANG["en-us"]["Report"] = "Report";
    $_LANG["en-us"]["ERROR_NOSERVERS"] = "Zur Zeit keine Server in der Liste.";
    
    // includes/content/login.inc
    $_LANG["en-us"]["AdministratorLogin"] = "Administrator Login";
    $_LANG["en-us"]["Username"] = "Username";
    $_LANG["en-us"]["Password"] = "Passwort";
    $_LANG["en-us"]["ERROR_ALREADYLOGGEDIN"] = "You are already logged in.";
    
    // Buttons
    $_LANG["en-us"]["Add"] = "Hinzufügen";
    $_LANG["en-us"]["Modify"] = "Ändern";
    $_LANG["en-us"]["Remove"] = "Entfernen";
    $_LANG["en-us"]["Clear"] = "Löschen";
    $_LANG["en-us"]["Reset"] = "Reset";
    
    // includes/content/log.inc
    $_LANG["en-us"]["Log"] = "Log";
    $_LANG["en-us"]["Timestamp"] = "Zeitstempel";
    $_LANG["en-us"]["Event"] = "Event";
    $_LANG["en-us"]["LogCleared"] = "Log Cleared";
    
    // includes/content/manageadmin.inc
    $_LANG["en-us"]["Admin"] = "Admin";
    $_LANG["en-us"]["ConfirmPassword"] = "Confirm Password";
    $_LANG["en-us"]["ERROR_BADUSERNAME"] = "You specified a blank or invalid username.";
    $_LANG["en-us"]["ERROR_BADPASSWORD"] = "You specified a blank or invalid password.";
    $_LANG["en-us"]["ERROR_BADCONFIRMPASSWORD"] = "You specified a blank or invalid password.";
    $_LANG["en-us"]["ERROR_BADPASSWORDMATCH"] = "You password and confirm password do not match.";
    $_LANG["en-us"]["ERROR_BADADMINID"] = "You specified a blank or invalid administrator ID.";
    $_LANG["en-us"]["ERROR_ATLEASTONEADMIN"] = "You must have at least one administrator.";
    
    // includes/content/manageplugins.inc
    $_LANG["en-us"]["Plugins"] = "Plug-ins";
    $_LANG["en-us"]["Plugin"] = "Plug-in";
    $_LANG["en-us"]["Type"] = "Type";
    $_LANG["en-us"]["Game"] = "Game";
    $_LANG["en-us"]["Enabled"] = "Angeschaltet";
    $_LANG["en-us"]["disabled"] = "Ausgeschaltet";
    $_LANG["en-us"]["ERROR_NOPLUGINS"] = "Es konnten keine Plugins gefunden werden.";
    $_LANG["en-us"]["MSG_IMPROVEPERFORMANCE"] = "Each game requires its own plug-in. You must enable plug-ins before they will take effect. Disabling the plug-ins that you do not use will improve performance.";
    
    // includes/content/manageservers.inc
    $_LANG["en-us"]["Servers"] = "Servers";
    $_LANG["en-us"]["IP"] = "IP";
    $_LANG["en-us"]["Port"] = "Port";
    $_LANG["en-us"]["ERROR_NOPLUGINSCANNOTADD"] = "Du must ein Plugin einschalten um einen Server hinzufügen zu können.";
    $_LANG["en-us"]["ERROR_BADSERVERID"] = "Es wurde eine leere oder falsche Server ID angegeben.";
    $_LANG["en-us"]["ERROR_BADIP"] = "Leere oder falsche Server IP.";
    $_LANG["en-us"]["ERROR_BADPORT"] = "Leerer oder falscher Server Port.";
    $_LANG["en-us"]["ERROR_BADGAME"] = "Leeres oder falsches Game.";
    
    // includes/content/settings.inc
    $_LANG["en-us"]["Note"] = "Note"; // Context: "Please Note: blah blah"
    $_LANG["en-us"]["Settings"] = "Settings";
    $_LANG["en-us"]["SiteName"] = "Site Name";
    $_LANG["en-us"]["SiteURL"] = "Site URL";
    $_LANG["en-us"]["ConnectionTimeout"] = "Connection Timeout";
    $_LANG["en-us"]["StreamInterval"] = "Stream Interval"; // Stream means a 'stream of information', a flow of data, like 'streaming video'.
    $_LANG["en-us"]["StreamTimeLimit"] = "Stream Time Limit";
    $_LANG["en-us"]["Style"] = "Style";
    $_LANG["en-us"]["Language"] = "Language";
    
    // includes/content/updatecheck.inc
    $_LANG["en-us"]["UpdateCheck"] = "Update Check";
    $_LANG["en-us"]["Version"] = "Version";
    $_LANG["en-us"]["ERROR_UACONTACTFAILED"] = "Unable to contact United Admins servers.";
    $_LANG["en-us"]["MSG_NEWVERSIONAVAILABLE"] = "There is a new version of phpUA available!";
    $_LANG["en-us"]["MSG_LATESTVERSION"] = "You have the latest version of phpUA.";
    
    // includes/engine/auth.inc
    $_LANG["en-us"]["Guest"] = "Guest";
    
    // includes/engine/benchmark.inc
    $_LANG["en-us"]["ExecutionTime"] = "Execution Time";
    $_LANG["en-us"]["sec"] = "sec"; // Context: "5 'sec' remaining" (i.e. SEConds)
    $_LANG["en-us"]["default"] = "default";
    
    // includes/engine/config.inc
    $_LANG["en-us"]["ERROR_BADCHARS"] = "The configuration file cannot accept '#', '=' or ':' characters.";
    
    // includes/engine/content.inc
    $_LANG["en-us"]["ERROR_PLUGINNOTFOUND"] = "The plug-in associated with this server has been disabled or could not be found.";
    $_LANG["en-us"]["ERROR_BADMODE"] = "You specified a blank or invalid mode.";
        
    // includes/engine/errorhandler.inc
    $_LANG["en-us"]["Forum"] = "Forum";
    $_LANG["en-us"]["ERROR_PHPERROR"] = "An unpredicted behavior has occurred in the phpUA Engine.";
    
    // Watch
    // Here, the user is presented with a OK and CANCEL dialog box.
    // To write a new line in the dialog box, use the code: \\n
    $_LANG["en-us"]["ERROR_TIMELIMITREACHED"] = "phpUA Message:\\n\\nYou have reached the Watch time limit.\\nWould you like to continue watching?\\n\\nTo continue watching, click the OK button.\\nTo return to the Server Index, click the Cancel button.";
    
    // UT
    $_LANG["en-us"]["Info"] = "Info";
    $_LANG["en-us"]["Hostname"] = "Hostname";
    $_LANG["en-us"]["Address"] = "Address";
    $_LANG["en-us"]["Version"] = "Version";
    $_LANG["en-us"]["Modification"] = "Modification";
    $_LANG["en-us"]["Mode"] = "Mode";
    $_LANG["en-us"]["Map"] = "Map";
    $_LANG["en-us"]["Players"] = "Players";
    $_LANG["en-us"]["Rules"] = "Rules";
    $_LANG["en-us"]["Watch"] = "Watch";
    $_LANG["en-us"]["Name"] = "Name";
    $_LANG["en-us"]["Team"] = "Team";
    $_LANG["en-us"]["Frags"] = "Frags";
    $_LANG["en-us"]["Mesh"] = "Mesh";
    $_LANG["en-us"]["Ping"] = "Ping";
    
    // BF
    $_LANG["en-us"]["Dedicated"] = "Dedicated"; // Context: "That Linux machine is a dedicated server"
    $_LANG["en-us"]["Password"] = "Password";
    $_LANG["en-us"]["RoundTime"] = "Round Time";
    $_LANG["en-us"]["ReservedSlots"] = "Reserved Slots";
    $_LANG["en-us"]["Tickets"] = "Tickets";
    $_LANG["en-us"]["Score"] = "Score";
    $_LANG["en-us"]["Deaths"] = "Deaths";
    $_LANG["en-us"]["PunkBuster"] = "PunkBuster";
    
    // HL
    $_LANG["en-us"]["Resource"] = "Resource";
    $_LANG["en-us"]["Hive"] = "Hive";
    $_LANG["en-us"]["Command"] = "Command";
    $_LANG["en-us"]["Protocol"] = "Protocol";
    $_LANG["en-us"]["Secure"] = "Secure";
    $_LANG["en-us"]["Insecure"] = "Insecure";
    $_LANG["en-us"]["Bots"] = "Bots";
    $_LANG["en-us"]["LAN"] = "LAN"; // Short for: Local Area Network
    $_LANG["en-us"]["Listen"] = "Listen"; // Context: "That Windows machine is a listen server"
    $_LANG["en-us"]["Required"] = "Required"; // Context: "A password is required to play on this server"
    $_LANG["en-us"]["None"] = "None";
    $_LANG["en-us"]["none"] = "none";
    $_LANG["en-us"]["connecting"] = "connecting";
    $_LANG["en-us"]["Infinite"] = "Infinite";
    $_LANG["en-us"]["DEAD"] = "DEAD";
    $_LANG["en-us"]["Hostage"] = "Hostage";
    $_LANG["en-us"]["Flag"] = "Flag";
    $_LANG["en-us"]["Dispenser"] = "Dispenser";
    $_LANG["en-us"]["Sentry"] = "Sentry";
    $_LANG["en-us"]["Teleporter"] = "Teleporter";
    $_LANG["en-us"]["Bomb"] = "Bomb";
    $_LANG["en-us"]["Planted"] = "Planted"; // Context: "The Bomb has been Planted"
    $_LANG["en-us"]["NotPlanted"] = "Not Planted"; // Context: "The Bomb is Not Planted"
    $_LANG["en-us"]["SentryLevel"] = "Sentry Level"; // Context: "Sentry Level 2"
    $_LANG["en-us"]["Kills"] = "Kills";
    $_LANG["en-us"]["Health"] = "Health";
    $_LANG["en-us"]["Armor"] = "Armor";
    $_LANG["en-us"]["Model"] = "Model";
    $_LANG["en-us"]["Weapon"] = "Weapon";
    $_LANG["en-us"]["Chat"] = "Chat";
    $_LANG["en-us"]["Type"] = "Type";
    $_LANG["en-us"]["Addons"] = "Add-ons";
    $_LANG["en-us"]["PlayingTime"] = "Playing Time";
    $_LANG["en-us"]["TimeLeft"] = "Time Left";
    
    endBenchmark(__FILE__, __FUNCTION__);
?>