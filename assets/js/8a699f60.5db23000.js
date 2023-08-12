"use strict";(self.webpackChunklansuite_documentation=self.webpackChunklansuite_documentation||[]).push([[524],{3905:(e,t,r)=>{r.d(t,{Zo:()=>c,kt:()=>f});var n=r(7294);function i(e,t,r){return t in e?Object.defineProperty(e,t,{value:r,enumerable:!0,configurable:!0,writable:!0}):e[t]=r,e}function a(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);t&&(n=n.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),r.push.apply(r,n)}return r}function o(e){for(var t=1;t<arguments.length;t++){var r=null!=arguments[t]?arguments[t]:{};t%2?a(Object(r),!0).forEach((function(t){i(e,t,r[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):a(Object(r)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(r,t))}))}return e}function l(e,t){if(null==e)return{};var r,n,i=function(e,t){if(null==e)return{};var r,n,i={},a=Object.keys(e);for(n=0;n<a.length;n++)r=a[n],t.indexOf(r)>=0||(i[r]=e[r]);return i}(e,t);if(Object.getOwnPropertySymbols){var a=Object.getOwnPropertySymbols(e);for(n=0;n<a.length;n++)r=a[n],t.indexOf(r)>=0||Object.prototype.propertyIsEnumerable.call(e,r)&&(i[r]=e[r])}return i}var u=n.createContext({}),s=function(e){var t=n.useContext(u),r=t;return e&&(r="function"==typeof e?e(t):o(o({},t),e)),r},c=function(e){var t=s(e.components);return n.createElement(u.Provider,{value:t},e.children)},d="mdxType",p={inlineCode:"code",wrapper:function(e){var t=e.children;return n.createElement(n.Fragment,{},t)}},m=n.forwardRef((function(e,t){var r=e.components,i=e.mdxType,a=e.originalType,u=e.parentName,c=l(e,["components","mdxType","originalType","parentName"]),d=s(r),m=i,f=d["".concat(u,".").concat(m)]||d[m]||p[m]||a;return r?n.createElement(f,o(o({ref:t},c),{},{components:r})):n.createElement(f,o({ref:t},c))}));function f(e,t){var r=arguments,i=t&&t.mdxType;if("string"==typeof e||i){var a=r.length,o=new Array(a);o[0]=m;var l={};for(var u in t)hasOwnProperty.call(t,u)&&(l[u]=t[u]);l.originalType=e,l[d]="string"==typeof e?e:i,o[1]=l;for(var s=2;s<a;s++)o[s]=r[s];return n.createElement.apply(null,o)}return n.createElement.apply(null,r)}m.displayName="MDXCreateElement"},9476:(e,t,r)=>{r.r(t),r.d(t,{assets:()=>u,contentTitle:()=>o,default:()=>p,frontMatter:()=>a,metadata:()=>l,toc:()=>s});var n=r(7462),i=(r(7294),r(3905));const a={id:"production-release",title:"Creating a Release",sidebar_position:5},o=void 0,l={unversionedId:"development/production-release",id:"development/production-release",title:"Creating a Release",description:"Introduction",source:"@site/docs/development/production-release.md",sourceDirName:"development",slug:"/development/production-release",permalink:"/lansuite/docs/development/production-release",draft:!1,editUrl:"https://github.com/lansuite/lansuite/tree/master/website/docs/development/production-release.md",tags:[],version:"current",sidebarPosition:5,frontMatter:{id:"production-release",title:"Creating a Release",sidebar_position:5},sidebar:"documentationSidebar",previous:{title:"Documentation",permalink:"/lansuite/docs/development/documentation"}},u={},s=[{value:"Introduction",id:"introduction",level:2},{value:"Usage",id:"usage",level:2},{value:"Building the image",id:"building-the-image",level:3},{value:"Building a release from the latest version",id:"building-a-release-from-the-latest-version",level:3},{value:"Building a release from a tag",id:"building-a-release-from-a-tag",level:3},{value:"Archives",id:"archives",level:3}],c={toc:s},d="wrapper";function p(e){let{components:t,...r}=e;return(0,i.kt)(d,(0,n.Z)({},c,r,{components:t,mdxType:"MDXLayout"}),(0,i.kt)("h2",{id:"introduction"},"Introduction"),(0,i.kt)("p",null,"The production release is not the same as the LanSuite development version (aka the GitHub repository).\nThe production release only contains the required files to run LanSuite as a website.\nIt does not contain any functionality to develop the platform."),(0,i.kt)("h2",{id:"usage"},"Usage"),(0,i.kt)("p",null,"To build a production release, we are using Docker.\nThis way, we ensure that every contributor can release the same production release with the same version constraints."),(0,i.kt)("h3",{id:"building-the-image"},"Building the image"),(0,i.kt)("p",null,"First step: Building the docker image to create a release:"),(0,i.kt)("pre",null,(0,i.kt)("code",{parentName:"pre"},"docker build --file ./Dockerfile-Production-Release --tag lansuite/lansuite:prod-release .\n")),(0,i.kt)("h3",{id:"building-a-release-from-the-latest-version"},"Building a release from the latest version"),(0,i.kt)("p",null,"If you aim to build a production release from the latest git HEAD:"),(0,i.kt)("pre",null,(0,i.kt)("code",{parentName:"pre"},"docker run  --rm --volume=./builds:/builds:rw lansuite/lansuite:prod-release\n")),(0,i.kt)("h3",{id:"building-a-release-from-a-tag"},"Building a release from a tag"),(0,i.kt)("p",null,"If you want to build a production release from a git tag:"),(0,i.kt)("pre",null,(0,i.kt)("code",{parentName:"pre"},'docker run  --rm --volume=./builds:/builds:rw -e "LANSUITE_VERSION=v4.2-beta" lansuite/lansuite:prod-release\n')),(0,i.kt)("p",null,"Please replace ",(0,i.kt)("inlineCode",{parentName:"p"},"v4.2-beta")," with your git tag in the command."),(0,i.kt)("h3",{id:"archives"},"Archives"),(0,i.kt)("p",null,"In your ",(0,i.kt)("inlineCode",{parentName:"p"},"./builds/")," folder, you now have two files:"),(0,i.kt)("ul",null,(0,i.kt)("li",{parentName:"ul"},"1 x tar.gz, which is the compressed LanSuite production release"),(0,i.kt)("li",{parentName:"ul"},"1 x file with a checksum of the ",(0,i.kt)("inlineCode",{parentName:"li"},".tar.gz")," file")))}p.isMDXComponent=!0}}]);