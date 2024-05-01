"use strict";(self.webpackChunklansuite_documentation=self.webpackChunklansuite_documentation||[]).push([[6114],{4879:(e,n,t)=>{t.r(n),t.d(n,{assets:()=>c,contentTitle:()=>a,default:()=>p,frontMatter:()=>o,metadata:()=>i,toc:()=>l});var r=t(5893),s=t(1151);const o={id:"performance-measurement",title:"Performance measurement",sidebar_position:6},a=void 0,i={id:"development/performance-measurement",title:"Performance measurement",description:"Flame graphs",source:"@site/docs/development/performance-measurement.md",sourceDirName:"development",slug:"/development/performance-measurement",permalink:"/lansuite/docs/development/performance-measurement",draft:!1,unlisted:!1,editUrl:"https://github.com/lansuite/lansuite/tree/master/website/docs/development/performance-measurement.md",tags:[],version:"current",sidebarPosition:6,frontMatter:{id:"performance-measurement",title:"Performance measurement",sidebar_position:6},sidebar:"documentationSidebar",previous:{title:"Folder structure",permalink:"/lansuite/docs/development/folder-structure"},next:{title:"Other",permalink:"/lansuite/docs/category/other"}},c={},l=[{value:"Flame graphs",id:"flame-graphs",level:2}];function d(e){const n={a:"a",code:"code",h2:"h2",li:"li",ol:"ol",p:"p",pre:"pre",...(0,s.a)(),...e.components};return(0,r.jsxs)(r.Fragment,{children:[(0,r.jsx)(n.h2,{id:"flame-graphs",children:"Flame graphs"}),"\n",(0,r.jsxs)(n.p,{children:["The LanSuite developer setup supports generation of ",(0,r.jsx)(n.a,{href:"https://xdebug.org/docs/trace",children:"xDebug traces"}),".\nThose traces can be used to generate a ",(0,r.jsx)(n.a,{href:"https://www.brendangregg.com/flamegraphs.html",children:"flame graph"}),"."]}),"\n",(0,r.jsx)(n.p,{children:"This guide shows you how to generate a flame graph.\nIt assumes you have a local development setup running.\nThis guide is not meant for your production site."}),"\n",(0,r.jsxs)(n.ol,{children:["\n",(0,r.jsxs)(n.li,{children:["\n",(0,r.jsxs)(n.p,{children:["Get a local copy of ",(0,r.jsx)(n.a,{href:"https://github.com/brendangregg/FlameGraph",children:"https://github.com/brendangregg/FlameGraph"})]}),"\n"]}),"\n",(0,r.jsxs)(n.li,{children:["\n",(0,r.jsx)(n.p,{children:"Start LanSuite via the docker-compose setup:"}),"\n",(0,r.jsx)(n.pre,{children:(0,r.jsx)(n.code,{children:"docker-compose up\n"})}),"\n"]}),"\n",(0,r.jsxs)(n.li,{children:["\n",(0,r.jsxs)(n.p,{children:["Run a website call with the GET parameter ",(0,r.jsx)(n.code,{children:"?XDEBUG_TRACE=lansuite"})," like ",(0,r.jsx)(n.code,{children:"http://127.0.0.1:8080/?XDEBUG_TRACE=lansuite"})]}),"\n"]}),"\n",(0,r.jsxs)(n.li,{children:["\n",(0,r.jsxs)(n.p,{children:["A new trace is generated and stored inside your root directory of the source code.\nIt is called like ",(0,r.jsx)(n.code,{children:"xdebug.trace.1689363817._code_index_php.xt.gz"})]}),"\n"]}),"\n",(0,r.jsxs)(n.li,{children:["\n",(0,r.jsx)(n.p,{children:"Unpack the trace"}),"\n",(0,r.jsx)(n.pre,{children:(0,r.jsx)(n.code,{children:"gunzip xdebug.trace.1689363817._code_index_php.xt.gz\n"})}),"\n"]}),"\n",(0,r.jsxs)(n.li,{children:["\n",(0,r.jsx)(n.p,{children:"Switch to your local copy of the FlameGraph repository and call"}),"\n",(0,r.jsx)(n.pre,{children:(0,r.jsx)(n.code,{children:"php stackcollapse-xdebug.php ../lansuite/xdebug.trace.1689363817._code_index_php.xt | ./flamegraph.pl > lansuite.svg\n"})}),"\n"]}),"\n",(0,r.jsxs)(n.li,{children:["\n",(0,r.jsxs)(n.p,{children:["Open ",(0,r.jsx)(n.code,{children:"lansuite.svg"})," and you should see something like"]}),"\n",(0,r.jsx)("img",{src:"/lansuite/img/flamegraph/lansuite.svg"}),"\n"]}),"\n"]})]})}function p(e={}){const{wrapper:n}={...(0,s.a)(),...e.components};return n?(0,r.jsx)(n,{...e,children:(0,r.jsx)(d,{...e})}):d(e)}},1151:(e,n,t)=>{t.d(n,{Z:()=>i,a:()=>a});var r=t(7294);const s={},o=r.createContext(s);function a(e){const n=r.useContext(o);return r.useMemo((function(){return"function"==typeof e?e(n):{...n,...e}}),[n,e])}function i(e){let n;return n=e.disableParentContext?"function"==typeof e.components?e.components(s):e.components||s:a(e.components),r.createElement(o.Provider,{value:n},e.children)}}}]);