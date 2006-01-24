;<? /*
;*************************************************************************
;*
;*	Lansuite - Webbased LAN-Party Management System
;*	-----------------------------------------------
;*
;*	(c) 2001-2004 by One-Network.Org
;*
;*	Lansuite Version:	2.0.3 RC1
;*	Filename: 		config.php
;*	Module: 		Framework
;*	Main editor: 		denny@one-network.org
;*	Description: 		This is the master lansuite config file
;*				This file should'nt be edited manually!
;*				Please use the script's settings menu for
;*				any changes!
;*
;**************************************************************************


[lansuite]
version				= LANsuite V2.0.6 RC2
default_design			= osX
user_timeout			= 300
chmod_dir			= 777
chmod_file			= 666

[environment]
configured			= 0
dir				= 
os				= 
mq				= 0
gd				= 0
snmp				= 0
ftp				= 0


[lanparty]
wwcl_party_id			= 0
wwcl_orgateam_id		= 0

[size]
userid_digits			= 4
search_results			= 30
comments			= 5
buddies				= 20
msgrefreshtime			= 15
log				= 10
pics				= 5
table_rows			= 30
guestlist			= 50
guestbook			= 15

[database]
server				= localhost
user				= root
passwd				= 
database			= lansuite
prefix				= lansuite_
db_admin_userid			= 0

[server_stats]
status				= 1
uptime				= 0
meminfo				= 0
cpuinfo				= 0
ifconfig			= 0
loadavg				= 0
ls_getinfo			= 1

;*/ ?>
