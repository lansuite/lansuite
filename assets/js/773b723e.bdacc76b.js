"use strict";(self.webpackChunklansuite_documentation=self.webpackChunklansuite_documentation||[]).push([[3117],{2762:(e,n,i)=>{i.r(n),i.d(n,{assets:()=>a,contentTitle:()=>s,default:()=>h,frontMatter:()=>t,metadata:()=>r,toc:()=>d});var o=i(5893),l=i(1151);const t={id:"upgrade",title:"Upgrade to a newer version",sidebar_position:1},s=void 0,r={id:"guides/upgrade",title:"Upgrade to a newer version",description:"Prerequisites",source:"@site/docs/guides/guides-upgrade.md",sourceDirName:"guides",slug:"/guides/upgrade",permalink:"/lansuite/docs/guides/upgrade",draft:!1,unlisted:!1,editUrl:"https://github.com/lansuite/lansuite/tree/master/website/docs/guides/guides-upgrade.md",tags:[],version:"current",sidebarPosition:1,frontMatter:{id:"upgrade",title:"Upgrade to a newer version",sidebar_position:1},sidebar:"documentationSidebar",previous:{title:"Guides",permalink:"/lansuite/docs/category/guides"},next:{title:"Development",permalink:"/lansuite/docs/category/development"}},a={},d=[{value:"Prerequisites",id:"prerequisites",level:2},{value:"Preparation",id:"preparation",level:2},{value:"Upgrade communications",id:"upgrade-communications",level:3},{value:"Download an official release",id:"download-an-official-release",level:3},{value:"Set system to read-only",id:"set-system-to-read-only",level:3},{value:"Execute a backup",id:"execute-a-backup",level:3},{value:"Backup the database",id:"backup-the-database",level:4},{value:"Backup your current installation",id:"backup-your-current-installation",level:4},{value:"Server Upload",id:"server-upload",level:3},{value:"Upgrade",id:"upgrade",level:2},{value:"Copy over files from an old installation",id:"copy-over-files-from-an-old-installation",level:3},{value:"Configuration: <code>environment.configured</code>",id:"configuration-environmentconfigured",level:3},{value:"Run specific release tasks",id:"run-specific-release-tasks",level:3},{value:"Execute LanSuite upgrade logic",id:"execute-lansuite-upgrade-logic",level:3},{value:"Test your installation",id:"test-your-installation",level:3},{value:"Unlock installation",id:"unlock-installation",level:3},{value:"Rollback (if things doesn&#39;t work)",id:"rollback-if-things-doesnt-work",level:2},{value:"Import DB backup",id:"import-db-backup",level:3},{value:"Restore folder backup",id:"restore-folder-backup",level:3},{value:"Pitfalls and Known Bugs",id:"pitfalls-and-known-bugs",level:2},{value:"Upgrade from LanSuite v4.2 to v5.0",id:"upgrade-from-lansuite-v42-to-v50",level:2},{value:"Configuration: <code>database.sqlmode</code>",id:"configuration-databasesqlmode",level:3},{value:"Configuration: <code>database.charset</code>",id:"configuration-databasecharset",level:3},{value:"Offline conversion",id:"offline-conversion",level:4},{value:"Online conversion",id:"online-conversion",level:4},{value:"Force latin1 as workaround",id:"force-latin1-as-workaround",level:4},{value:"Configuration: <code>google_maps_key</code>",id:"configuration-google_maps_key",level:3},{value:"Fonts in <code>ext_inc/pdf_fonts</code>",id:"fonts-in-ext_incpdf_fonts",level:3},{value:"Files in <code>ext_inc/user_pics</code>",id:"files-in-ext_incuser_pics",level:3},{value:"Removal of files of deprecates leagues like WWCL, NGL and LGZ files",id:"removal-of-files-of-deprecates-leagues-like-wwcl-ngl-and-lgz-files",level:3},{value:"Converting custom fonts",id:"converting-custom-fonts",level:3},{value:"Server module: Change of hardware information from Megabyte to Gigabyte (for RAM) and from Megaherz to Gigaherz for CPU",id:"server-module-change-of-hardware-information-from-megabyte-to-gigabyte-for-ram-and-from-megaherz-to-gigaherz-for-cpu",level:3}];function c(e){const n={a:"a",code:"code",em:"em",h2:"h2",h3:"h3",h4:"h4",li:"li",ol:"ol",p:"p",pre:"pre",ul:"ul",...(0,l.a)(),...e.components};return(0,o.jsxs)(o.Fragment,{children:[(0,o.jsx)(n.h2,{id:"prerequisites",children:"Prerequisites"}),"\n",(0,o.jsxs)(n.ul,{children:["\n",(0,o.jsx)(n.li,{children:"Your target system needs to run PHP >= 8.1 and MySQL >= 5.7"}),"\n",(0,o.jsx)(n.li,{children:"A fair bit of time"}),"\n"]}),"\n",(0,o.jsx)(n.h2,{id:"preparation",children:"Preparation"}),"\n",(0,o.jsx)(n.h3,{id:"upgrade-communications",children:"Upgrade communications"}),"\n",(0,o.jsx)(n.p,{children:"It is recommended to tell your users (and fellow administrators) in advance that your LanSuite instance won't be 100% functional available during the upgrade."}),"\n",(0,o.jsx)(n.p,{children:"That should be well done in advance if you run a larger installation, for smaller ones no one may notice. But better be safe than sorry."}),"\n",(0,o.jsx)(n.h3,{id:"download-an-official-release",children:"Download an official release"}),"\n",(0,o.jsxs)(n.ol,{children:["\n",(0,o.jsxs)(n.li,{children:["Download the release from ",(0,o.jsx)(n.a,{href:"https://github.com/lansuite/lansuite/releases",children:"GitHubs Releases page"})]}),"\n",(0,o.jsx)(n.li,{children:"Extract the archive on your machine"}),"\n"]}),"\n",(0,o.jsx)(n.h3,{id:"set-system-to-read-only",children:"Set system to read-only"}),"\n",(0,o.jsx)(n.p,{children:"It is recommended to lock your installation and avoid changes during/after the backup.\nThis can either be done by either of the following:"}),"\n",(0,o.jsxs)(n.ul,{children:["\n",(0,o.jsx)(n.li,{children:'Lock LanSuite by enabling the corresponding option under "Admin-Page" -> "Common Settings" -> "Lock LanSuite page"'}),"\n",(0,o.jsx)(n.li,{children:"Shut down public access to your web server"}),"\n",(0,o.jsx)(n.li,{children:"Move index.php to a different location"}),"\n"]}),"\n",(0,o.jsx)(n.h3,{id:"execute-a-backup",children:"Execute a backup"}),"\n",(0,o.jsx)(n.h4,{id:"backup-the-database",children:"Backup the database"}),"\n",(0,o.jsx)(n.p,{children:"Create a full database backup before upgrading."}),"\n",(0,o.jsx)(n.p,{children:"There are multiple variants available:"}),"\n",(0,o.jsxs)(n.ul,{children:["\n",(0,o.jsx)(n.li,{children:'LanSuite XML export ("Admin-Page" -> "Export" -> "XML - Full Database")'}),"\n",(0,o.jsxs)(n.li,{children:["MySQL commandline dump (Something like ",(0,o.jsx)(n.code,{children:"mysqldump --quick -u<ls user> -p <dbname> > exportfile.sql"}),")"]}),"\n",(0,o.jsx)(n.li,{children:"PHPMyAdmin - if installed"}),"\n",(0,o.jsx)(n.li,{children:"Any tool provided by your hoster"}),"\n"]}),"\n",(0,o.jsxs)(n.p,{children:["Backing up your database using ",(0,o.jsx)(n.code,{children:"mysqldump"})," is recommended, as both, LanSuite and PHPMyAdmin, may hit server limits on larger installations."]}),"\n",(0,o.jsx)(n.h4,{id:"backup-your-current-installation",children:"Backup your current installation"}),"\n",(0,o.jsxs)(n.ul,{children:["\n",(0,o.jsxs)(n.li,{children:["If you have command line access: ",(0,o.jsx)(n.code,{children:"tar cfvz ls_backup.tar.gz <ls_folder>"})," or ",(0,o.jsx)(n.code,{children:"cp -R <ls_folder> <ls_folder>.backup"})]}),"\n",(0,o.jsx)(n.li,{children:"Download the whole folder with (S)FTP or any other remote access tool you have available"}),"\n"]}),"\n",(0,o.jsx)(n.h3,{id:"server-upload",children:"Server Upload"}),"\n",(0,o.jsx)(n.p,{children:"Upload all files into a different folder to your server.\nIf you executed the previous step already on your server, then obviously nothing to be done at this step."}),"\n",(0,o.jsx)(n.h2,{id:"upgrade",children:"Upgrade"}),"\n",(0,o.jsx)(n.h3,{id:"copy-over-files-from-an-old-installation",children:"Copy over files from an old installation"}),"\n",(0,o.jsx)(n.p,{children:"Replace the new files with the old ones."}),"\n",(0,o.jsx)(n.p,{children:"A few files and locations are unique to your installation.\nThose should be kept.\nLike:"}),"\n",(0,o.jsxs)(n.ul,{children:["\n",(0,o.jsxs)(n.li,{children:["The base configuration file from ",(0,o.jsx)(n.code,{children:"inc/base/config.php"})]}),"\n",(0,o.jsxs)(n.li,{children:["Anything under ",(0,o.jsx)(n.code,{children:"ext_inc"})]}),"\n",(0,o.jsxs)(n.li,{children:["Custom designs from ",(0,o.jsx)(n.code,{children:"designs/"})]}),"\n"]}),"\n",(0,o.jsx)(n.p,{children:"Copy them to the same place on the new installation, overwriting everything there."}),"\n",(0,o.jsxs)(n.h3,{id:"configuration-environmentconfigured",children:["Configuration: ",(0,o.jsx)(n.code,{children:"environment.configured"})]}),"\n",(0,o.jsxs)(n.p,{children:["In order to simplify some configuration steps and updates it is recommended to reset the configuration back to unconfigured and run through the installer again.\nThis updates tables and menu entries and configures some standard options.\nThis can be done by editing the following line in ",(0,o.jsx)(n.code,{children:"inc/base/config.php"})]}),"\n",(0,o.jsx)(n.pre,{children:(0,o.jsx)(n.code,{children:"[...]\n[environment]\nconfigured = 0\n[...]\n"})}),"\n",(0,o.jsxs)(n.p,{children:["...just ",(0,o.jsx)(n.em,{children:"DO NOT select the option to overwrite the existing DB"})]}),"\n",(0,o.jsx)(n.h3,{id:"run-specific-release-tasks",children:"Run specific release tasks"}),"\n",(0,o.jsx)(n.p,{children:"It may be required to run additional steps to prepare everything for the new version.\nPlease check the upgrade guide to your specific version below."}),"\n",(0,o.jsx)(n.h3,{id:"execute-lansuite-upgrade-logic",children:"Execute LanSuite upgrade logic"}),"\n",(0,o.jsxs)(n.p,{children:['Visit "Admin-Page" -> "Lansuite updaten / reparieren" ->> "Datenbank updaten und verwalten".\nThis is also available at ',(0,o.jsx)(n.code,{children:"http(s)://<your-domain>/index.php?mod=install&action=db"}),"."]}),"\n",(0,o.jsx)(n.h3,{id:"test-your-installation",children:"Test your installation"}),"\n",(0,o.jsx)(n.p,{children:"Now is the time to look if everything is as expected.\nIf yes, then you are good to unlock the installation.\nIf not then either see what broke in the various logs or go to Rollback."}),"\n",(0,o.jsx)(n.h3,{id:"unlock-installation",children:"Unlock installation"}),"\n",(0,o.jsx)(n.p,{children:'If you locked your installation, you  can now unlock it again.\n"Admin-Page" -> "Common Settings" -> "Lock LanSuite page".'}),"\n",(0,o.jsx)(n.h2,{id:"rollback-if-things-doesnt-work",children:"Rollback (if things doesn't work)"}),"\n",(0,o.jsx)(n.p,{children:"In case your upgrade failed, and you need to return to your old state the following needs to be done:"}),"\n",(0,o.jsx)(n.h3,{id:"import-db-backup",children:"Import DB backup"}),"\n",(0,o.jsxs)(n.p,{children:["You have to reimport the database backup taken before.\nHow this is done varies in what tools you have available.\nThe command line call for this would be ",(0,o.jsx)(n.code,{children:"mysql -u<ls_user> -p < exportfile.sql"}),"."]}),"\n",(0,o.jsx)(n.h3,{id:"restore-folder-backup",children:"Restore folder backup"}),"\n",(0,o.jsxs)(n.p,{children:["Next step is to restore the folder backup taken.\nFirst clean up (",(0,o.jsx)(n.code,{children:"rm -rf <failed_update_folder>"}),") or move away (",(0,o.jsx)(n.code,{children:"mv <failed_update_folder> <anyothername>"}),") the failed installation.\nThen restore the original installation from your backup.\nEither by extracting (",(0,o.jsx)(n.code,{children:"gunzip ls_backup.tar.gz && tar xvf ls_backup.tar"}),") or moving back the copied folder (",(0,o.jsx)(n.code,{children:"mv <ls_folder>.backup <ls_folder>"}),").\nOr: Re-uploading your backup, if that was the way you went."]}),"\n",(0,o.jsx)(n.h2,{id:"pitfalls-and-known-bugs",children:"Pitfalls and Known Bugs"}),"\n",(0,o.jsxs)(n.ul,{children:["\n",(0,o.jsx)(n.li,{children:"Please ensure that export and import of Database images use the same character encoding. Using the same client on the same system should ensure this, but be cautious if dump and import are done on different systems/clients."}),"\n",(0,o.jsxs)(n.li,{children:["The git ",(0,o.jsx)(n.code,{children:"master"})," branch is not usable without pulling in additional resources via composer. You must do this first to obtain a runnable installation!"]}),"\n",(0,o.jsx)(n.li,{children:"IP Addresses from existing log entries are removed due to a change of the column format for IPv6 support"}),"\n"]}),"\n",(0,o.jsx)(n.h2,{id:"upgrade-from-lansuite-v42-to-v50",children:"Upgrade from LanSuite v4.2 to v5.0"}),"\n",(0,o.jsxs)(n.h3,{id:"configuration-databasesqlmode",children:["Configuration: ",(0,o.jsx)(n.code,{children:"database.sqlmode"})]}),"\n",(0,o.jsxs)(n.p,{children:["Add the following line to your configuration at ",(0,o.jsx)(n.code,{children:"/inc/base/config.php"}),":"]}),"\n",(0,o.jsx)(n.pre,{children:(0,o.jsx)(n.code,{children:'[...]\n[database]\nsqlmode = "NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION"\n[...]\n'})}),"\n",(0,o.jsxs)(n.h3,{id:"configuration-databasecharset",children:["Configuration: ",(0,o.jsx)(n.code,{children:"database.charset"})]}),"\n",(0,o.jsxs)(n.p,{children:["Old installations of LanSuite stored data in the database with character collation set to ",(0,o.jsx)(n.code,{children:"latin1"}),".\nBut LanSuite now uses ",(0,o.jsx)(n.code,{children:"utf8mb4"})," by default.\nThus it is either required to modify database collation or to force character set back to ",(0,o.jsx)(n.code,{children:"latin1"}),"."]}),"\n",(0,o.jsx)(n.h4,{id:"offline-conversion",children:"Offline conversion"}),"\n",(0,o.jsx)(n.p,{children:"Changing the collation requires the following steps:"}),"\n",(0,o.jsxs)(n.ul,{children:["\n",(0,o.jsx)(n.li,{children:"Full SQL dump of the database (see backup)"}),"\n",(0,o.jsx)(n.li,{children:"Drop all tables"}),"\n",(0,o.jsxs)(n.li,{children:["Change collation of database to ",(0,o.jsx)(n.code,{children:"utf8mb4"})," (or drop database and recreate)"]}),"\n",(0,o.jsxs)(n.li,{children:["Re-import SQL dump with setting collation of the connection / file to ",(0,o.jsx)(n.code,{children:"latin1"})]}),"\n"]}),"\n",(0,o.jsx)(n.h4,{id:"online-conversion",children:"Online conversion"}),"\n",(0,o.jsxs)(n.p,{children:["The following steps in PHPmyAdmin ",(0,o.jsx)(n.em,{children:"should"})," also work, this has not been confirmed yet:"]}),"\n",(0,o.jsx)(n.p,{children:'Select the database.\nClick the "Operations" tab.\nUnder "Collation" section, select the desired collation.\nClick the "Change all tables collations" checkbox.\nA new "Change all tables columns collations" checkbox will appear.\nClick the "Change all tables columns collations" checkbox.\nClick the "Go" button.'}),"\n",(0,o.jsx)(n.h4,{id:"force-latin1-as-workaround",children:"Force latin1 as workaround"}),"\n",(0,o.jsxs)(n.p,{children:["If you want to enforce ",(0,o.jsx)(n.code,{children:"latin1"})," as workaround you need to add the following key to the configuration file located at ",(0,o.jsx)(n.code,{children:"/inc/base/config.php"}),":"]}),"\n",(0,o.jsx)(n.pre,{children:(0,o.jsx)(n.code,{children:'[...]\n[database]\ncharset = "latin1"\n[...]\n'})}),"\n",(0,o.jsx)(n.p,{children:"** Do not change collation setting during normal operation and without doing the required conversion, this will cause data to be stored in both formats with no easy way to remediate **"}),"\n",(0,o.jsxs)(n.h3,{id:"configuration-google_maps_key",children:["Configuration: ",(0,o.jsx)(n.code,{children:"google_maps_key"})]}),"\n",(0,o.jsx)(n.p,{children:'A separate API Key has been introduced for usage of Google Maps. (#887)\nCheck the bottom of "Admin-Page" -> "Common Settings" where the existing setting for the Analytics ID is located and add/copy the API key to be used for Google Maps API requests\nMap display will be nonfunctional until the key is added.'}),"\n",(0,o.jsxs)(n.h3,{id:"fonts-in-ext_incpdf_fonts",children:["Fonts in ",(0,o.jsx)(n.code,{children:"ext_inc/pdf_fonts"})]}),"\n",(0,o.jsx)(n.p,{children:"Delete the following files"}),"\n",(0,o.jsxs)(n.ul,{children:["\n",(0,o.jsx)(n.li,{children:(0,o.jsx)(n.code,{children:"ext_inc/pdf_fonts/courier.php"})}),"\n",(0,o.jsx)(n.li,{children:(0,o.jsx)(n.code,{children:"ext_inc/pdf_fonts/helvetica.php"})}),"\n",(0,o.jsx)(n.li,{children:(0,o.jsx)(n.code,{children:"ext_inc/pdf_fonts/helveticab.php"})}),"\n",(0,o.jsx)(n.li,{children:(0,o.jsx)(n.code,{children:"ext_inc/pdf_fonts/helveticabi.php"})}),"\n",(0,o.jsx)(n.li,{children:(0,o.jsx)(n.code,{children:"ext_inc/pdf_fonts/helveticai.php"})}),"\n",(0,o.jsx)(n.li,{children:(0,o.jsx)(n.code,{children:"ext_inc/pdf_fonts/symbol.php"})}),"\n",(0,o.jsx)(n.li,{children:(0,o.jsx)(n.code,{children:"ext_inc/pdf_fonts/times.php"})}),"\n",(0,o.jsx)(n.li,{children:(0,o.jsx)(n.code,{children:"ext_inc/pdf_fonts/timesb.php"})}),"\n",(0,o.jsx)(n.li,{children:(0,o.jsx)(n.code,{children:"ext_inc/pdf_fonts/timesbi.php"})}),"\n",(0,o.jsx)(n.li,{children:(0,o.jsx)(n.code,{children:"ext_inc/pdf_fonts/timesi.php"})}),"\n",(0,o.jsx)(n.li,{children:(0,o.jsx)(n.code,{children:"ext_inc/pdf_fonts/zapfdingbats.php"})}),"\n"]}),"\n",(0,o.jsx)(n.p,{children:"Delete the following folder"}),"\n",(0,o.jsxs)(n.ul,{children:["\n",(0,o.jsx)(n.li,{children:(0,o.jsx)(n.code,{children:"ext_inc/pdf_fonts/makefont"})}),"\n"]}),"\n",(0,o.jsxs)(n.p,{children:["Add the following files from the release package to the ",(0,o.jsx)(n.code,{children:"ext_inc/pdf_fonts"})," folder"]}),"\n",(0,o.jsxs)(n.ul,{children:["\n",(0,o.jsx)(n.li,{children:(0,o.jsx)(n.code,{children:"ext_inc/pdf_fonts/.gitkeep"})}),"\n"]}),"\n",(0,o.jsxs)(n.h3,{id:"files-in-ext_incuser_pics",children:["Files in ",(0,o.jsx)(n.code,{children:"ext_inc/user_pics"})]}),"\n",(0,o.jsx)(n.p,{children:"Delete the following files"}),"\n",(0,o.jsxs)(n.ul,{children:["\n",(0,o.jsx)(n.li,{children:(0,o.jsx)(n.code,{children:"ext_inc/user_pics/info.txt"})}),"\n",(0,o.jsx)(n.li,{children:(0,o.jsx)(n.code,{children:"ext_inc/user_pics/pic_.jpg"})}),"\n"]}),"\n",(0,o.jsxs)(n.p,{children:["Add the following files from the release package to the ",(0,o.jsx)(n.code,{children:"ext_inc/user_pics"})," folder"]}),"\n",(0,o.jsxs)(n.ul,{children:["\n",(0,o.jsx)(n.li,{children:(0,o.jsx)(n.code,{children:"ext_inc/user_pics/.gitkeep"})}),"\n"]}),"\n",(0,o.jsx)(n.h3,{id:"removal-of-files-of-deprecates-leagues-like-wwcl-ngl-and-lgz-files",children:"Removal of files of deprecates leagues like WWCL, NGL and LGZ files"}),"\n",(0,o.jsx)(n.p,{children:"Delete the following files:"}),"\n",(0,o.jsxs)(n.ul,{children:["\n",(0,o.jsx)(n.li,{children:(0,o.jsx)(n.code,{children:"ext_inc/tournament_rules/gameini.xml"})}),"\n",(0,o.jsx)(n.li,{children:(0,o.jsx)(n.code,{children:"ext_inc/tournament_rules/games.xml"})}),"\n",(0,o.jsx)(n.li,{children:(0,o.jsx)(n.code,{children:"ext_inc/tournament_rules/xml_games.xml"})}),"\n",(0,o.jsx)(n.li,{children:(0,o.jsx)(n.code,{children:"ext_inc/tournament_rules/info.txt"})}),"\n"]}),"\n",(0,o.jsx)(n.p,{children:"Delete the following folders:"}),"\n",(0,o.jsxs)(n.ul,{children:["\n",(0,o.jsx)(n.li,{children:(0,o.jsx)(n.code,{children:"ext_inc/tournament_icons/leagues/"})}),"\n"]}),"\n",(0,o.jsx)(n.h3,{id:"converting-custom-fonts",children:"Converting custom fonts"}),"\n",(0,o.jsxs)(n.p,{children:["If you have added your own custom fonts into ",(0,o.jsx)(n.code,{children:"ext_inc/pdf_fonts"}),", you need to convert them into the new fpdf format:"]}),"\n",(0,o.jsxs)(n.ol,{children:["\n",(0,o.jsxs)(n.li,{children:["Go to ",(0,o.jsx)(n.a,{href:"http://www.fpdf.org/makefont/",children:"http://www.fpdf.org/makefont/"})]}),"\n",(0,o.jsx)(n.li,{children:"Choose your custom font file (ttf)"}),"\n",(0,o.jsx)(n.li,{children:"Download the generated *.z and *.php file"}),"\n",(0,o.jsxs)(n.li,{children:["Add the ",(0,o.jsx)(n.code,{children:"*.ttf"}),", ",(0,o.jsx)(n.code,{children:"*.z"})," and ",(0,o.jsx)(n.code,{children:"*.php"})," into ",(0,o.jsx)(n.code,{children:"ext_inc/pdf_fonts"})]}),"\n"]}),"\n",(0,o.jsx)(n.h3,{id:"server-module-change-of-hardware-information-from-megabyte-to-gigabyte-for-ram-and-from-megaherz-to-gigaherz-for-cpu",children:"Server module: Change of hardware information from Megabyte to Gigabyte (for RAM) and from Megaherz to Gigaherz for CPU"}),"\n",(0,o.jsx)(n.p,{children:"The meaning of the fields following fields has changed:"}),"\n",(0,o.jsx)(n.p,{children:"Before:"}),"\n",(0,o.jsxs)(n.ul,{children:["\n",(0,o.jsx)(n.li,{children:(0,o.jsx)(n.code,{children:"CPU (MHz)"})}),"\n",(0,o.jsx)(n.li,{children:(0,o.jsx)(n.code,{children:"RAM (MB)"})}),"\n"]}),"\n",(0,o.jsx)(n.p,{children:"After:"}),"\n",(0,o.jsxs)(n.ul,{children:["\n",(0,o.jsx)(n.li,{children:(0,o.jsx)(n.code,{children:"CPU (GHz)"})}),"\n",(0,o.jsx)(n.li,{children:(0,o.jsx)(n.code,{children:"RAM (GB)"})}),"\n"]}),"\n",(0,o.jsx)(n.p,{children:"LanSuite is not taking care about the update of your data.\nPlease go to the Server module and change the CPU and RAM values for your servers manually."})]})}function h(e={}){const{wrapper:n}={...(0,l.a)(),...e.components};return n?(0,o.jsx)(n,{...e,children:(0,o.jsx)(c,{...e})}):c(e)}},1151:(e,n,i)=>{i.d(n,{Z:()=>r,a:()=>s});var o=i(7294);const l={},t=o.createContext(l);function s(e){const n=o.useContext(t);return o.useMemo((function(){return"function"==typeof e?e(n):{...n,...e}}),[n,e])}function r(e){let n;return n=e.disableParentContext?"function"==typeof e.components?e.components(l):e.components||l:s(e.components),o.createElement(t.Provider,{value:n},e.children)}}}]);