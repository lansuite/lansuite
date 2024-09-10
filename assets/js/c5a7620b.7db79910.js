"use strict";(self.webpackChunklansuite_documentation=self.webpackChunklansuite_documentation||[]).push([[261],{7957:(e,t,n)=>{n.r(t),n.d(t,{assets:()=>a,contentTitle:()=>d,default:()=>o,frontMatter:()=>s,metadata:()=>l,toc:()=>c});var r=n(4848),i=n(8453);const s={id:"settings",title:"Settings",sidebar_position:1},d=void 0,l={id:"modules/settings",title:"Settings",description:"Module Description",source:"@site/docs/modules/Info2.md",sourceDirName:"modules",slug:"/modules/settings",permalink:"/lansuite/docs/modules/settings",draft:!1,unlisted:!1,editUrl:"https://github.com/lansuite/lansuite/tree/master/website/docs/modules/Info2.md",tags:[],version:"current",sidebarPosition:1,frontMatter:{id:"settings",title:"Settings",sidebar_position:1},sidebar:"documentationSidebar",previous:{title:"Modules",permalink:"/lansuite/docs/category/modules"},next:{title:"Other",permalink:"/lansuite/docs/category/other"}},a={},c=[{value:"Module Description",id:"module-description",level:2},{value:"Configuration options",id:"configuration-options",level:2},{value:"Placeholders and replacement values",id:"placeholders-and-replacement-values",level:2},{value:"User-Related",id:"user-related",level:3},{value:"Party Related",id:"party-related",level:2},{value:"Entrance Fee related",id:"entrance-fee-related",level:2}];function h(e){const t={h2:"h2",h3:"h3",p:"p",table:"table",tbody:"tbody",td:"td",th:"th",thead:"thead",tr:"tr",...(0,i.R)(),...e.components};return(0,r.jsxs)(r.Fragment,{children:[(0,r.jsx)(t.h2,{id:"module-description",children:"Module Description"}),"\n",(0,r.jsx)(t.p,{children:"This module provides the abilitity to create and manage multiple rich-text pages for user information.\nMultiple default pages are provided as example / template including generic party information, how to reach the party, participation rules and so on."}),"\n",(0,r.jsx)(t.h2,{id:"configuration-options",children:"Configuration options"}),"\n",(0,r.jsxs)(t.table,{children:[(0,r.jsx)(t.thead,{children:(0,r.jsxs)(t.tr,{children:[(0,r.jsx)(t.th,{children:"Option"}),(0,r.jsx)(t.th,{children:"Impact"}),(0,r.jsx)(t.th,{children:"Default value"})]})}),(0,r.jsxs)(t.tbody,{children:[(0,r.jsxs)(t.tr,{children:[(0,r.jsx)(t.td,{children:"Use WYSIWYG-Editor"}),(0,r.jsx)(t.td,{children:"Loads FCKedit for editing of Info pages, raw HTML-Input field will be used otherwise"}),(0,r.jsx)(t.td,{children:"Yes"})]}),(0,r.jsxs)(t.tr,{children:[(0,r.jsx)(t.td,{children:"Add new Entries as subentries of info2"}),(0,r.jsx)(t.td,{children:"If enabled new & enabled entries will be automatically added as submenu-Item for the Module"}),(0,r.jsx)(t.td,{children:"Yes"})]})]})]}),"\n",(0,r.jsx)(t.h2,{id:"placeholders-and-replacement-values",children:"Placeholders and replacement values"}),"\n",(0,r.jsx)(t.p,{children:"The following placeholders can be used at the moment in info texts and will be replaced on display with the related values.\nThe placeholder name will be displayed if the value cannot be resolved"}),"\n",(0,r.jsx)(t.h3,{id:"user-related",children:"User-Related"}),"\n",(0,r.jsxs)(t.table,{children:[(0,r.jsx)(t.thead,{children:(0,r.jsxs)(t.tr,{children:[(0,r.jsx)(t.th,{children:"Variable"}),(0,r.jsx)(t.th,{children:"Replacement value"})]})}),(0,r.jsxs)(t.tbody,{children:[(0,r.jsxs)(t.tr,{children:[(0,r.jsx)(t.td,{children:"%USERID%"}),(0,r.jsx)(t.td,{children:"The numeric ID of the user"})]}),(0,r.jsxs)(t.tr,{children:[(0,r.jsx)(t.td,{children:"%USERNAME%"}),(0,r.jsx)(t.td,{children:"The username (nickname)"})]})]})]}),"\n",(0,r.jsx)(t.h2,{id:"party-related",children:"Party Related"}),"\n",(0,r.jsx)(t.p,{children:"These values are replaced if a party is selected"}),"\n",(0,r.jsxs)(t.table,{children:[(0,r.jsx)(t.thead,{children:(0,r.jsxs)(t.tr,{children:[(0,r.jsx)(t.th,{children:"Variable"}),(0,r.jsx)(t.th,{children:"Replacement value"})]})}),(0,r.jsxs)(t.tbody,{children:[(0,r.jsxs)(t.tr,{children:[(0,r.jsx)(t.td,{children:"%PARTYID%"}),(0,r.jsx)(t.td,{children:"The numeric ID of the currently selected party for the user"})]}),(0,r.jsxs)(t.tr,{children:[(0,r.jsx)(t.td,{children:"%PARTYMAME%"}),(0,r.jsx)(t.td,{children:"The given name for the party"})]}),(0,r.jsxs)(t.tr,{children:[(0,r.jsx)(t.td,{children:"%PARTYBEGIN%"}),(0,r.jsxs)(t.td,{children:["The party start time and date in format hh",":mm"," dd.mm.yyyy"]})]}),(0,r.jsxs)(t.tr,{children:[(0,r.jsx)(t.td,{children:"%PARTYEND%"}),(0,r.jsxs)(t.td,{children:["The party end time and date in format hh",":mm"," dd.mm.yyyy"]})]}),(0,r.jsxs)(t.tr,{children:[(0,r.jsx)(t.td,{children:"%PARTYGUESTS%"}),(0,r.jsx)(t.td,{children:"The maximum amount of participants"})]}),(0,r.jsxs)(t.tr,{children:[(0,r.jsx)(t.td,{children:"%PARTYLOCATION%"}),(0,r.jsx)(t.td,{children:"The location given for the party"})]})]})]}),"\n",(0,r.jsx)(t.h2,{id:"entrance-fee-related",children:"Entrance Fee related"}),"\n",(0,r.jsx)(t.p,{children:"User must be registered with a price selected for values to be replaced"}),"\n",(0,r.jsxs)(t.table,{children:[(0,r.jsx)(t.thead,{children:(0,r.jsxs)(t.tr,{children:[(0,r.jsx)(t.th,{children:"Variable"}),(0,r.jsx)(t.th,{children:"Replacement value"})]})}),(0,r.jsxs)(t.tbody,{children:[(0,r.jsxs)(t.tr,{children:[(0,r.jsx)(t.td,{children:"%PARTYPRICEID%"}),(0,r.jsx)(t.td,{children:"If the user is already registered for the party then this will reflect the price ID"})]}),(0,r.jsxs)(t.tr,{children:[(0,r.jsx)(t.td,{children:"%PARTYPRICETEXT%"}),(0,r.jsx)(t.td,{children:"The name of the ticket price item given"})]}),(0,r.jsxs)(t.tr,{children:[(0,r.jsx)(t.td,{children:"%PARTYPRICEVALUE%"}),(0,r.jsx)(t.td,{children:"The amount defined for the price item"})]})]})]})]})}function o(e={}){const{wrapper:t}={...(0,i.R)(),...e.components};return t?(0,r.jsx)(t,{...e,children:(0,r.jsx)(h,{...e})}):h(e)}},8453:(e,t,n)=>{n.d(t,{R:()=>d,x:()=>l});var r=n(6540);const i={},s=r.createContext(i);function d(e){const t=r.useContext(s);return r.useMemo((function(){return"function"==typeof e?e(t):{...t,...e}}),[t,e])}function l(e){let t;return t=e.disableParentContext?"function"==typeof e.components?e.components(i):e.components||i:d(e.components),r.createElement(s.Provider,{value:t},e.children)}}}]);