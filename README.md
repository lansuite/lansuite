
	Lansuite - Webbased LAN-Party Management System
	-----------------------------------------------
		
	(c) 2001-2006 by One-Network.Org
	
	
INSTALLATION
============
	
	(1) Um Lansuite zu installieren stellen Sie zunächst sicher dass ein Webserver (z.B. Apache), 
	ein MySQL-Server (Ab Version 4.0) sowie PHP (Ab Version 4.3) installiert sind. 
	
	(2) Danach kopieren Sie bitte alle Lansuite-Dateien in den "DocumentRoot" Ihres Webservers (oder einen Unterordner darin).
	Bei den meisten Webservern heisst dieser Ordner "htdocs".
	
	(3) Nun öffnen Sie einen Browser und geben als Adresse die IP Nummer des Servers an, auf dem
	Lansuite installiert wurde. Sollten dies der gleiche Rechner sein, an dem Sie den Browser geöffnet haben,
	geben Sie bitte "http://127.0.0.1" ein.
	
	(4) Da Lansuite noch nicht konfiguriert ist,  werden Sie direkt zu einem Setup Assistenten weiter-
	geleitet, der Sie durch die Konfiguration von Lansuite führt.
	
	Halten Sie dazu bitte die Daten Ihres MySQL-Servers (Server-Adresse - meist 127.0.0.1 -, Benutzername,
	Passwort und Datenbank) bereit.
	Wärend der Installation werden Sie des weiteren dazu aufgefordert, den Daten-Export aus LANsurfer
	anzugeben, der anschließend importiert wird. Diesen können Sie direkt von der LANsurfer Website 
	(http://www.lansurfer.de) kopieren.
	
	(5) Sowie die Installation abgeschlossen ist, kann Lansuite benutzt werden.
	Jeder Gast/Orga dessen Rechner nun über ein Netzwerk mit dem Server verbunden ist, kann auf Lansuite 
	zugreifen, indem er in einem Browser die IP Nummer des Server eingibt (z.B. http://192.168.1.1).
	

WEITERE HILFEN - ONLINE
=======================
	
	Sollten Sie Probleme mit der Installation haben, oder weitere Fragen zum Arbeiten mit Lansuite haben, schauen Sie doch mal
	im Online-Doku-Wiki unter http://lansuite-docu.orgapage.de vorbei. Hier steht dir eine inzwischen sehr ausführliche und
	ständig aktuallisierte Dokumentation zu Lansuite bereit.

	Falls Sie Fehler im System finden, oder Feature-Wünsche äußern möchten, so verwenden Sie dazu bitte unseren Bugtracker:
  http://bugtracking.one-network.org

	Für Diskussionen und sonstige Fragen steht dir unser Board unter http://board.one-network.org zur Verfügung.


SYSTEMVORAUSSETZUNGEN
=====================

	Hardware
	--------
	
		Je nach Anzahl der User variiert die Hardwarevoraussetzung.
		Hier einge empfohlene Richtlinien:
		
		unter 100 User:	486 100 Mhz	 + (od. kompatibel)	- 64  MB Ram
		ab 100 User:	Pentium 200 Mhz	 + (od. kompatibel)	- 64  MB Ram
		ab 200 User:	Pentium 500 Mhz	 + (od. kompatibel)	- 128 MB Ram
		ab 500 User:	Pentium 1000 Mhz + (od. kompatibel)	- 256 MB Ram
		ab 1000 User:	Dual Pentium 800 Mhz + (od. kompatibel)	- 1024 MB Ram
		
		Diese Wert sind reine Schätzungen und beruhen noch nicht auf Messwerten! Es kann daher keinerlei 
		Verantwortung für diese Empfehlungen übernommen werden.
		
		! Ab einer Usergrenze von 500 Usern ist es dringend empfehlenswert die Datenbank (MySQL-Server) 
		! auf einen seperaten Server auszulagern.
	
	Software
	--------
	
		Empfohlene Betriebssysteme: Linux, FreeBSD, OpenBSD, Solaris9+, Microsoft Windows NT 4.0+, Windows 2000,  
								Windows .net Server, 
		
		- PHP kompatibler Webserver. Empfohlen: Apache ab Version 1.3
		- MySQL ab Version 3.2
		- PHP ab Version 4.3+
		- PHP Module: 
			- FTP
			- SNMP
			- GD-LIBRARY (mit Freetype2-Support)
	
	Sonstiges
	---------
	
		Um das Lansuite Modul "Downloads" nutzen zu können muss ein FTP-Server auf einem von dem Webserver 
		verschiedenen Server installiert sein.
