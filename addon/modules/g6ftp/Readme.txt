

     ************************************
     ****Mein FTP Account / LS2 Modul****
     ************************************



Pre Alpha Version

/////////////////////////////

1. Voraussetzung für dieses Modul/Script ist ein !! Local !! installierter FTP Server "Gene6 FTP Server v.3.x".

Anpassung:

Öffne die /modules/g6ftp/data/config.inc --->> Passe diese zeilen an:
---------------------------------------------------------------------

$FtpDomainName = "deine Domain";                         // die zuzutreffende Domain
$FtpServPath = "C:/Programme/Gene6 FTP Server/"; // DIR der installierten Serversoftware *default

Kopiere die /modules/g6ftp/NEWWEBUSER.ini in den Ordner:
--------------------------------------------------------
C:\*Programme\Gene6 FTP Server*\Accounts\*deineDomain*\users    // *ggf. anpassen

==================================================================================

Starte den FTP Server und passe den User NEWWEBUSER an (Freigabe Ordner e.c.)

*NEWWEBUSER wird als default vom Modul genutzt.

==================================================================================

Modul Support www.xtreme-bits.de @ Kuppe

==================================================================================

changelog:

0.1 PreAlpha (LS2 Modul Veröffentlichung)
