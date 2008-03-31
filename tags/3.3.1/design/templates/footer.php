<?php
$dsp->SetVar('version', $templ['index']['info']['version']);
$dsp->SetVar('year', date('y'));
$dsp->SetVar('db-querys', $db->count_query);
if ($_GET['design'] != 'base') $dsp->SetVar('processed-in', round($sitetool->out_work(), 2));
$dsp->SetVar('url-base', $CurentURLBase);
if ($cfg['sys_optional_footer']) $dsp->SetVar('footer-line', HTML_NEWLINE . $cfg['sys_optional_footer']);


?>
<a  href="ext_inc/newsfeed/news.xml" title="Latest news feed"><img src="ext_inc/footer_buttons/button-rss-grey.png" onmouseover="this.src='ext_inc/footer_buttons/button-rss.png'" onmouseout="this.src='ext_inc/footer_buttons/button-rss-grey.png'" width="80" height="15" alt="Latest news feed" border="0" /></a>
<a  href="index.php?mod=about&amp;action=license" rel="license" title="GNU General Public License"><img src="ext_inc/footer_buttons/button-gpl-grey.png" onmouseover="this.src='ext_inc/footer_buttons/button-gpl.png'" onmouseout="this.src='ext_inc/footer_buttons/button-gpl-grey.png'" width="80" height="15" alt="GNU General Public License" border="0" /></a>
<a  href="https://www.paypal.com/xclick/business=jochen.jung%40gmx.de&amp;item_name=Lansuite&amp;no_shipping=2&amp;no_note=1&amp;tax=0&amp;currency_code=EUR&amp;lc=DE" title="Donate"><img src="ext_inc/footer_buttons/button-donate-grey.png" onmouseover="this.src='ext_inc/footer_buttons/button-donate.png'" onmouseout="this.src='ext_inc/footer_buttons/button-donate-grey.png'" alt="Donate" width="80" height="15" border="0" /></a>
<a  href="http://www.php.net" title="Powered by PHP"><img src="ext_inc/footer_buttons/button-php-grey.png" onmouseover="this.src='ext_inc/footer_buttons/button-php.png'" onmouseout="this.src='ext_inc/footer_buttons/button-php-grey.png'" width="80" height="15" alt="Powered by PHP" border="0" /></a>
<a  href="http://www.mysql.com" title="MySQL Database"><img src="ext_inc/footer_buttons/button-mysql-grey.png" onmouseover="this.src='ext_inc/footer_buttons/button-mysql.png'" onmouseout="this.src='ext_inc/footer_buttons/button-mysql-grey.png'" width="80" height="15" alt="MySQL Database" border="0" /></a>
<!--
<a  href="http://validator.w3.org/check/referer" title="Valid XHTML 1.0"><img src="ext_inc/footer_buttons/grey_button-xhtml.png" width="80" height="15" alt="Valid XHTML 1.0" border="0" /></a>
<a  href="http://jigsaw.w3.org/css-validator/check/referer" title="Valid CSS"><img src="ext_inc/footer_buttons/grey_button-css.png" width="80" height="15" alt="Valid CSS" border="0" /></a>
-->
<a  href="http://www.lansuite.de" title="Lansuite"><img src="ext_inc/footer_buttons/button-lansuite-grey.png" onmouseover="this.src='ext_inc/footer_buttons/button_lansuite.png'" onmouseout="this.src='ext_inc/footer_buttons/button-lansuite-grey.png'" width="80" height="15" alt="Lansuite" border="0" /></a>
<br />
<span class="footer"><a href="index.php?mod=about" class="menu"><?$dsp->EchoVar('version')?> &copy;2001-<?$dsp->EchoVar('year')?></a>
 | DB-Querys: <?$dsp->EchoVar('db-querys')?>
 | Processed in: <?$dsp->EchoVar('processed-in')?> Sec
 | <a href="<?$dsp->EchoVar('url-base')?>&amp;fullscreen=yes" class="menu">Fullscreen</a>
 <?$dsp->EchoVar('footer-line')?></span>
