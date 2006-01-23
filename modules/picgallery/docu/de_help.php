<?

$helplet['modul'] = 'Bildergalerie';
$helplet['action'] = 'Anzeigen';
$helplet['info'] = 'In diesem Modul können Bilder der Lanparty gepostet, und so den übrigen Gästen zugänglich gemacht werden';

$helplet['key'][1] = 'FTP-Upload';
$helplet['value'][1] = 'Die Bilder lassen sich über das Webinterface (mittels dem "Hochladen"-Button), oder aber auch per FTP, oder Windows-Freigabe hochladen. Dazu muss einfach das Verzeichnis "ext_inc/picgallery/" freigegeben werden. Alle darin abgelegten bilder werden von Lansuite automatisch erkannt.';
$helplet['key'][2] = 'Untergalerien';
$helplet['value'][2] = 'Durch einfaches Anlegen von Unterordnern in oben genanntem Ordner lassen sich beliebig tief verschachtelte Untergalerien anlegen. Die Ordner können natürlich auch alternativ über das Webinterface angelegt werden';
$helplet['key'][3] = 'Automatisch generierte Thumbnails';
$helplet['value'][3] = 'Zur besseren Übersicht werden automatisch beim ersten Aufruf Thumbnails generiert und dem Benutzer so eine Übersicht über die Bilder der Galerie geboten';
$helplet['key'][4] = 'Bildformat';
$helplet['value'][4] = 'LanSuite kann mit den 3 üblichen Webgrafik-Formaten umgehen: Jpeg, Png und Gif. Bei Gif-Bildern ist jedoch zu beachten, dass bei Älteren PHP-Versionen hier keine Thumbnails generiert werden können. Um zu sehen, ob dies bei Ihnen funktioniert, werfen Sie einen Blick auf die Version und Komponenten Ihrer GD-Bibliothek (Zu sehen auf der Admin-Seite bei den Systemvorraussetzungen).';

?>