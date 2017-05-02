<?php

if ($auth['username']) {
    $_POST["username"] = $auth['username'];
}
if ($auth['firstname']) {
    $_POST["firstname"] = $auth['firstname'];
}
if ($auth['name']) {
    $_POST["name"] = $auth['name'];
}

if ($cfg["irc_server"] == "null" or $cfg["irc_server"] == "") {
    $func->error(t('Kein IRC Server in den Moduleinstellungen definiert.'));
} elseif ($cfg["irc_width"] == "" or $cfg["irc_height"] == "") {
    $func->error(t('Keine Gr&ouml;&szlig;enangaben in den Moduleinstellungen definiert.'));
} elseif (!$_POST["username"]) {
    $dsp->NewContent(t('Chat'), t('Hier kannst du auf unserem IRC Server chatten.'));
    $dsp->SetForm("index.php?mod=irc");

    $dsp->AddTextFieldRow("username", t('Benutzername'), $_POST["username"], "");
    $dsp->AddTextFieldRow("firstname", t('Vorname'), $_POST["firstname"], "");
    $dsp->AddTextFieldRow("name", t('Nachname'), $_POST["name"], "");

    $dsp->AddFormSubmitRow(t('Hinzufügen'));
    $dsp->AddContent();
} else {
    if ($language == "en") {
        $lang_out = "english";
    } else {
        $lang_out = "german";
    }

    $dsp->NewContent(t('Chat'), t('Hier kannst du auf unserem IRC Server chatten.'));
    $dsp->AddSingleRow(HTML_NEWLINE . '<div align=center><applet code=IRCApplet.class codebase="ext_scripts/pjirc/" archive="irc.jar,pixx.jar" width=' . $cfg["irc_width"] . ' height=' . $cfg["irc_height"] . '>
	<param name="CABINETS" value="irc.cab,securedirc.cab,pixx.cab">

	<param name="nick" value="' . $_POST["username"] . '">
	<param name="alternatenick" value="' . $_POST["username"] . '???">
	<param name="name" value="' . $_POST["firstname"] ." ". $_POST["name"] . '">
	<param name="host" value="' . $cfg["irc_server"] . '">
	<param name="gui" value="pixx">
	<param name="pixx:showhelp" value="false">
	<param name="command1" value="join '. $cfg["irc_channel"] .'">
	<param name="language" value="$lang_out">

	<param name="quitmessage" value="User went offline">
	<param name="asl" value="true">
	<param name="useinfo" value="true">

	<param name="style:bitmapsmileys" value="true">
	<param name="style:smiley1" value=":) img/sourire.gif">
	<param name="style:smiley2" value=":-) img/sourire.gif">
	<param name="style:smiley3" value=":-D img/content.gif">
	<param name="style:smiley4" value=":d img/content.gif">
	<param name="style:smiley5" value=":-O img/OH-2.gif">
	<param name="style:smiley6" value=":o img/OH-1.gif">
	<param name="style:smiley7" value=":-P img/langue.gif">
	<param name="style:smiley8" value=":p img/langue.gif">
	<param name="style:smiley9" value=";-) img/clin-oeuil.gif">
	<param name="style:smiley10" value=";) img/clin-oeuil.gif">
	<param name="style:smiley11" value=":-( img/triste.gif">
	<param name="style:smiley12" value=":( img/triste.gif">
	<param name="style:smiley13" value=":-| img/OH-3.gif">
	<param name="style:smiley14" value=":| img/OH-3.gif">
	<param name="style:smiley15" value=":\'( img/pleure.gif">
	<param name="style:smiley16" value=":$ img/rouge.gif">
	<param name="style:smiley17" value=":-$ img/rouge.gif">
	<param name="style:smiley18" value="(H) img/cool.gif">
	<param name="style:smiley19" value="(h) img/cool.gif">
	<param name="style:smiley20" value=":-@ img/enerve1.gif">
	<param name="style:smiley21" value=":@ img/enerve2.gif">
	<param name="style:smiley22" value=":-S img/roll-eyes.gif">
	<param name="style:smiley23" value=":s img/roll-eyes.gif">
	<param name="style:backgroundimage" value="true">
	<param name="style:backgroundimage1" value="all all 0 background.gif">
	<param name="style:sourcefontrule1" value="all all Serif 12">
	<param name="style:floatingasl" value="true">

	<param name="pixx:timestamp" value="true">
	<param name="pixx:highlight" value="true">
	<param name="pixx:highlightnick" value="true">
	<param name="pixx:nickfield" value="true">
	<param name="pixx:styleselector" value="true">
	<param name="pixx:setfontonstyle" value="true">

	</applet></div>
	'. HTML_NEWLINE);

    $dsp->AddContent();
}
