<?php
$dsp->NewContent(LANSUITE_VERSION, 'A web based lanparty administration tool');

$dsp->AddSingleRow('<b>Contact</b>', 'align="center"');
$dsp->AddSingleRow('Internet/Development: <a href="https://github.com/lansuite/lansuite" title="lansuite at Github">https://github.com/lansuite/lansuite</a>', 'align="center"');

$dsp->AddSingleRow('<b>Thanks to all project contributos</b>', 'align="center"');
$dsp->AddSingleRow('Please head over to <a href="https://github.com/lansuite/lansuite/blob/master/CONTRIBUTORS.md" title="All lansuite contributors">project contributors</a> to see the full list of project contributors.', 'align="center"');

$dsp->AddSingleRow('<b>Thanks and Greets go to</b>', 'align="center"');
$dsp->AddSingleRow('an alle aktiven User im Board<br />
<a href="http://blog.one-network.org" target="_blank">OpenSource Intranet Blog (http://blog.one-network.org</a>)<br />
an alle Dokuschreiber<br />
<a href="http://lansuite-docu.orgapage.de" target="_blank">OpenSource Intranet Blog (http://lansuite-docu.orgapage.de</a>)<br />
Gigahertz Rent GmbH <br />
MySQL-Crew<br />
PHP-Crew<br />
php.net (Funktionsreferenz)<br />
Apache-Crew<br />
TortoiseSVN (http://tortoisesvn.net/)<br />
phpBB.com (http://www.phpbb.com)<br />
Adobe (Photoshop)<br />
Fraunhofer Institut (MPEG 1 Layer 3)<br />
Domain Factory GmbH<br />
Fox&amp;Pro7 (Simpsons)<br />
Spinnrad St. Wendel <br />
Gauloises Cigarettes (Raphael)<br />
The crazy old man who\'s complaining over all about the drunken teenagers at the trainstation<br />
All lanparty organisators<br />
Lanshock developers<br />
PHPChrystal developers<br />
Linus Torvalds <br />
Mailer-Daemon<br />
KDE-Team<br />
and all other friends', 'align="center"');

$dsp->AddSingleRow('<b>Lansuite consists of:</b>', 'align="center"');
$dsp->AddSingleRow('PHP: <!--PHP-LINES-START-->51268<!--PHP-LINES-STOP--> php-code lines and <!--PHP-CHARS-START-->2149482<!--PHP-CHARS-STOP--> chars<br />
HTML: <!--HTML-LINES-START-->12156<!--HTML-LINES-STOP--> html-code lines and <!--HTML-CHARS-START-->455622<!--HTML-CHARS-STOP--> chars', 'align="center"');

$dsp->AddBackButton("index.php?mod=about", "about/credits");
