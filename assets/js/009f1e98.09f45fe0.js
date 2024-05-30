"use strict";(self.webpackChunklansuite_documentation=self.webpackChunklansuite_documentation||[]).push([[3436],{4069:(e,n,t)=>{t.r(n),t.d(n,{assets:()=>o,contentTitle:()=>r,default:()=>h,frontMatter:()=>d,metadata:()=>c,toc:()=>a});var s=t(5893),i=t(1151);const d={id:"configuration",title:"Configuration",sidebar_position:3},r=void 0,c={id:"getting-started/configuration",title:"Configuration",description:"Configuration file",source:"@site/docs/getting-started/configuration.md",sourceDirName:"getting-started",slug:"/getting-started/configuration",permalink:"/lansuite/docs/getting-started/configuration",draft:!1,unlisted:!1,editUrl:"https://github.com/lansuite/lansuite/tree/master/website/docs/getting-started/configuration.md",tags:[],version:"current",sidebarPosition:3,frontMatter:{id:"configuration",title:"Configuration",sidebar_position:3},sidebar:"documentationSidebar",previous:{title:"Installation",permalink:"/lansuite/docs/getting-started/installation"},next:{title:"Settings",permalink:"/lansuite/docs/getting-started/settings"}},o={},a=[{value:"Configuration file",id:"configuration-file",level:2},{value:"Configuration settings",id:"configuration-settings",level:2}];function l(e){const n={code:"code",h2:"h2",p:"p",pre:"pre",strong:"strong",table:"table",tbody:"tbody",td:"td",th:"th",thead:"thead",tr:"tr",...(0,i.a)(),...e.components};return(0,s.jsxs)(s.Fragment,{children:[(0,s.jsx)(n.h2,{id:"configuration-file",children:"Configuration file"}),"\n",(0,s.jsxs)(n.p,{children:["LANSuite's configuration file is written in the ",(0,s.jsx)(n.code,{children:"ini"})," format and should be placed into ",(0,s.jsx)(n.code,{children:"inc/base/config.php"}),"."]}),"\n",(0,s.jsx)(n.p,{children:"An example configuration file looks like:"}),"\n",(0,s.jsx)(n.pre,{children:(0,s.jsx)(n.code,{className:"language-ini",children:"[lansuite]\ndefault_design=simple\nchmod_dir=777\nchmod_file=666\ndebugmode=0\n\n[database]\nserver=mysql\nuser=root\npasswd=\ndatabase=lansuite\nprefix=ls_\ncharset=utf8\n"})}),"\n",(0,s.jsxs)(n.p,{children:[(0,s.jsx)(n.strong,{children:"Warning"}),": Setting directories to ",(0,s.jsx)(n.code,{children:"0777"})," is not suggested for production. Only your web server user should be able to write into this directory."]}),"\n",(0,s.jsx)(n.h2,{id:"configuration-settings",children:"Configuration settings"}),"\n",(0,s.jsx)(n.p,{children:"Here you will find a detailed description of each configuration setting:"}),"\n",(0,s.jsxs)(n.table,{children:[(0,s.jsx)(n.thead,{children:(0,s.jsxs)(n.tr,{children:[(0,s.jsx)(n.th,{children:"Section"}),(0,s.jsx)(n.th,{children:"Name"}),(0,s.jsx)(n.th,{children:"Type"}),(0,s.jsx)(n.th,{children:"Description"})]})}),(0,s.jsxs)(n.tbody,{children:[(0,s.jsxs)(n.tr,{children:[(0,s.jsx)(n.td,{children:(0,s.jsx)(n.code,{children:"lansuite"})}),(0,s.jsx)(n.td,{children:(0,s.jsx)(n.code,{children:"default_design"})}),(0,s.jsx)(n.td,{children:"string"}),(0,s.jsxs)(n.td,{children:["Default design for the LANSuite instance. Users might be able to choose different designs. If no design is chosen, the default design is shown. Valid designs are ",(0,s.jsx)(n.code,{children:"simple"}),", ",(0,s.jsx)(n.code,{children:"osX"})," or ",(0,s.jsx)(n.code,{children:"Sunset"})]})]}),(0,s.jsxs)(n.tr,{children:[(0,s.jsx)(n.td,{children:(0,s.jsx)(n.code,{children:"lansuite"})}),(0,s.jsx)(n.td,{children:(0,s.jsx)(n.code,{children:"chmod_dir"})}),(0,s.jsx)(n.td,{children:"integer"}),(0,s.jsxs)(n.td,{children:["Newly created directories will be changed to this Unix access pattern. E.g. ",(0,s.jsx)(n.code,{children:"644"})," means read- and write access for the owner, read access for everyone else or ",(0,s.jsx)(n.code,{children:"600"})," means read- and write access for the owner, nothing for everyone else."]})]}),(0,s.jsxs)(n.tr,{children:[(0,s.jsx)(n.td,{children:(0,s.jsx)(n.code,{children:"lansuite"})}),(0,s.jsx)(n.td,{children:(0,s.jsx)(n.code,{children:"chmod_file"})}),(0,s.jsx)(n.td,{children:"integer"}),(0,s.jsxs)(n.td,{children:["Newly uploaded files will be changed to this Unix access pattern. E.g. ",(0,s.jsx)(n.code,{children:"644"})," means read- and write access for the owner, read access for everyone else or ",(0,s.jsx)(n.code,{children:"600"})," means read- and write access for the owner, nothing for everyone else."]})]}),(0,s.jsxs)(n.tr,{children:[(0,s.jsx)(n.td,{children:(0,s.jsx)(n.code,{children:"lansuite"})}),(0,s.jsx)(n.td,{children:(0,s.jsx)(n.code,{children:"uid"})}),(0,s.jsx)(n.td,{children:"string"}),(0,s.jsx)(n.td,{children:"Contains an auto-generated id to distinguish multiple instances of LanSuite when accessing shared ressources (e.g. cache )."})]}),(0,s.jsxs)(n.tr,{children:[(0,s.jsx)(n.td,{children:(0,s.jsx)(n.code,{children:"lansuite"})}),(0,s.jsx)(n.td,{children:(0,s.jsx)(n.code,{children:"debugmode"})}),(0,s.jsx)(n.td,{children:"boolean"}),(0,s.jsxs)(n.td,{children:["If it is enabled (",(0,s.jsx)(n.code,{children:"1"}),"), errors will be shown with a full stack trace. This is useful for development and debugging purpose. In production, this should be disabled with ",(0,s.jsx)(n.code,{children:"0"}),"."]})]}),(0,s.jsxs)(n.tr,{children:[(0,s.jsx)(n.td,{children:(0,s.jsx)(n.code,{children:"database"})}),(0,s.jsx)(n.td,{children:(0,s.jsx)(n.code,{children:"server"})}),(0,s.jsx)(n.td,{children:"string"}),(0,s.jsxs)(n.td,{children:["The hostname / IP address of the database server. For example, ",(0,s.jsx)(n.code,{children:"localhost"})," or ",(0,s.jsx)(n.code,{children:"mysql5.myhoster.com"})," or ",(0,s.jsx)(n.code,{children:"192.168.3.65"})]})]}),(0,s.jsxs)(n.tr,{children:[(0,s.jsx)(n.td,{children:(0,s.jsx)(n.code,{children:"database"})}),(0,s.jsx)(n.td,{children:(0,s.jsx)(n.code,{children:"user"})}),(0,s.jsx)(n.td,{children:"string"}),(0,s.jsx)(n.td,{children:"The username of the database user. LANSuite should have an own database user for the database connection."})]}),(0,s.jsxs)(n.tr,{children:[(0,s.jsx)(n.td,{children:(0,s.jsx)(n.code,{children:"database"})}),(0,s.jsx)(n.td,{children:(0,s.jsx)(n.code,{children:"passwd"})}),(0,s.jsx)(n.td,{children:"string"}),(0,s.jsx)(n.td,{children:"The password for the database user."})]}),(0,s.jsxs)(n.tr,{children:[(0,s.jsx)(n.td,{children:(0,s.jsx)(n.code,{children:"database"})}),(0,s.jsx)(n.td,{children:(0,s.jsx)(n.code,{children:"database"})}),(0,s.jsx)(n.td,{children:"string"}),(0,s.jsx)(n.td,{children:"The Name of the database which will be used by LANSuite. The user needs to have access to this database."})]}),(0,s.jsxs)(n.tr,{children:[(0,s.jsx)(n.td,{children:(0,s.jsx)(n.code,{children:"database"})}),(0,s.jsx)(n.td,{children:(0,s.jsx)(n.code,{children:"prefix"})}),(0,s.jsx)(n.td,{children:"string"}),(0,s.jsx)(n.td,{children:"Prefix that will be used for every database table. Every table created by LANSuite will be prefixed with the value entered here. This is used to avoid table name collisions and you can run multiple LANSuite instances and applications in one database."})]}),(0,s.jsxs)(n.tr,{children:[(0,s.jsx)(n.td,{children:(0,s.jsx)(n.code,{children:"database"})}),(0,s.jsx)(n.td,{children:(0,s.jsx)(n.code,{children:"charset"})}),(0,s.jsx)(n.td,{children:"string"}),(0,s.jsxs)(n.td,{children:["MySQL supported character set to be used for the database connection. ",(0,s.jsx)(n.code,{children:"utf8"})," is the default value and should be fine for new installations. Older installations of LANSuite (e.g. release 4.2) may need to use ",(0,s.jsx)(n.code,{children:"latin1"})," to avoid display issues."]})]})]})]})]})}function h(e={}){const{wrapper:n}={...(0,i.a)(),...e.components};return n?(0,s.jsx)(n,{...e,children:(0,s.jsx)(l,{...e})}):l(e)}},1151:(e,n,t)=>{t.d(n,{Z:()=>c,a:()=>r});var s=t(7294);const i={},d=s.createContext(i);function r(e){const n=s.useContext(d);return s.useMemo((function(){return"function"==typeof e?e(n):{...n,...e}}),[n,e])}function c(e){let n;return n=e.disableParentContext?"function"==typeof e.components?e.components(i):e.components||i:r(e.components),s.createElement(d.Provider,{value:n},e.children)}}}]);