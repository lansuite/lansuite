<?php

if (!$cfg["equipment_shopid"]) {
    $func->error(t('Es wurde noch keine Orgapage.Net-ShopID angegeben. Diese kann auf der Admin-Seite in den Moduleinstellungen unter \'Equipmentshop\' eingestellt werden'));
} else {
    $dsp->NewContent(t('Administration'), t('Equipmentliste Administrieren'));
    $dsp->AddSingleRow(t('Dieses Modul ermöglicht es einen <a href="http://www.orgapage.net" traget="_blank">Orgapage.Net<a>-EquipmentShop sehr einfach in eine LanSuite-Webseite zu integrieren.
    <br><br>In der <a href="http://www.orgapage.net/pages/equip/submit.php" traget="_blank">Equipmentliste auf Orgapage.Net</a> kannst du einen solchen Shop erstellen, falls du dies noch nicht getan hast. Von diesem werden dann die Daten ausgelesen, die hier in diesem Modul präsentiert werden. Durch das Eintragen wird das von dir angebotene Equipment auch in der Equipmentliste auf Orgapage angezeigt, so dass weitere Besucher evtl. auf deinen Shop aufmerksam werden.
    <br><br>Einen vorhandenen Shop kannst du unter <a href="http://orgapage.de/pages/equip/shops/admin/index.php" traget="_blank">http://orgapage.de/pages/equip/shops/admin/index.php/</a> verwalten (Bestellungen einsehen / auf reserviert schalten / löschen / ...).'));
    $dsp->AddContent();
}
