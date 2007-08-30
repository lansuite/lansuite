<?

$helplet['modul'] = 'WWCL-Code';
$helplet['action'] = 'Hilfe';
$helplet['info'] = 'Informationen zum Einfügen des WWCl-Codes';

$helplet['key'][1] = 'WWCL';
$helplet['value'][1] = 'Falls Sie WWCL-Turniere auf Ihrer Party spielen, sollten Sie unten stehenden Banner-Code auf Ihrer Sponsoren-Seite hinzufügen. Das Einfügen eines Buttons ist nicht notwendig, da in LanSuite automatisch eine WWCL-Box mit dem Button auf allen Turnier-Seiten erscheint, die WWCL-Spiele enthalten';
$helplet['key'][2] = 'Code';
$helplet['value'][2] = '&lt;!-- START OF WWCL BUTTON CODE 3.0-->
&lt;script language="JavaScript" type="text/javascript" src="http://dico.planetlan-gmbh.de/adx.js">&lt;/script>
&lt;script language="JavaScript" type="text/javascript">
&lt;!--
if (!document.phpAds_used) document.phpAds_used = ",";
phpAds_random = new String (Math.random()); phpAds_random = phpAds_random.substring(2,11);
document.write ("&lt;" + "script language=\'JavaScript\' type=\'text/javascript\' src=\'");
document.write ("http://dico.planetlan-gmbh.de/adjs.php?n=" + phpAds_random);
document.write ("&amp;what=zone:13&amp;target=_blank");
document.write ("&amp;exclude=" + document.phpAds_used);
if (document.referrer)
  document.write ("&amp;referer=" + escape(document.referrer));
document.write ("\'>&lt;" + "/script>");
//-->
&lt;/script>&lt;noscript>&lt;a href="http://dico.planetlan-gmbh.de/adclick.php?n=af5ab174" target="_blank">&lt;img src="http://dico.planetlan-gmbh.de/adview.php?what=zone:13&n=af5ab174" border="0" alt="">&lt;/a>&lt;/noscript>
&lt;!-- END OF WWCL BUTTON CODE -->';

?>
