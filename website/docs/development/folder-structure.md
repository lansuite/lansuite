---
id: folder-structure
title: Folder structure
sidebar_position: 6
---

## Introduction

In order to avoid unnecessary calculations of unchanged data or retrieval from slow sources LanSuite now uses an internal caching infrastructure.
When you are implementing new features it should be considered if content needs to be created on every call or if content can be cached and reused.
Also for the usage this has some implications as the content on some pages may not represent the latest state, but the cached content.

## Folder structure

```
├── design                          -> Storage for standard and your custom designs
│   ├── beamer
│   ├── images
│   ├── osX
│   ├── <your design here>
│   ├── simple
│   ├── style.css
│   ├── sunset
│   └── templates
├── ext_inc                         -> Storage for external files that belong to the instance itself, like user uploads and similar
│   ├── UserFiles                       -> Upload-Directory for FCK-Editor files
│   ├── avatare                         -> Upload-Directory for user avatare (module: usrmgr)
│   ├── banner                          -> Upload-Directory for sponsor banners (module: sponsor)
│   ├── barcodes                        -> Temp. storage for barcode generation (module: pdf)
│   ├── board_upload                    -> Files uploaded by users using the board (module: board)
│   ├── bugtracker_upload               -> Files uploaded by users using the bugtracker (module: bugtracker)
│   ├── downloads                       -> Contains files, which will be distributed using the downloads module  (module: downloads)
│   ├── fonts                           -> Storage for fonts used by Captcha, Barcode and image generation
│   ├── foodcenter                      -> Upload-Directory for files (module: foodcenter)
│   ├── foodcenter_templates            -> Storage for HTML templates (module: foodcenter)
│   ├── footer_buttons                  -> Storage for images used in the footer (design)
│   ├── home                            -> Storage for variable text (module: home)
│   ├── import                          -> Temp. storage for imported files (module: install)
│   ├── news_icons                      -> Storage for images used in the news (module: news)
│   ├── newsfeed                        -> Contain newsfeeds, which will be generated each time an admin posts news (module: news)
│   ├── noc                             -> Storage for images used in the noc module (module: noc)
│   ├── party_infos                     -> Contain stats and infos about LAN-Parties, generated as XML.
│   ├── pdf_fonts                       -> Storage for FPDF fonts
│   ├── pdf_templates                   -> Storage for images used for pdf generation (module: pdf)
│   ├── picgallery                      -> Contain user pictures and thumbnails of the image gallery (module: picgallery)
│   ├── picgallery_icon                 -> Storage for static images used in the image gallery (module: picgallery)
│   ├── smilies                         -> Storage for static images for smilies
│   ├── sticker
│   ├── team_banners                    -> Contains the banners of each team, registered to a tournament (module: tournament2)
│   ├── templates_c                     -> Compilation directory for the smarty template system
│   ├── templates_cache                 -> Cache directory for the smarty template system
│   ├── tournament_icons                -> Storage for images used for tournaments (module: tournament2)
│   ├── tournament_rules                -> Storage for tournaments rules (module: tournament2)
│   ├── tournament_screenshots          -> Contains tournament screenshots, the user upload, when submitting results  (module: tournament2)
│   └── user_pics                       -> Contains pictures of users. You can either upload them using the usermanagers edit function, or you can put them into this directory manualy, using the filename picXXX.jpg, where XXX is the users userid (without leeding zeros) (module: usrmgr)
├── ext_scripts                     -> Folder for external scripts used by LANSuite
├── inc                             -> LANSuite source code
│   ├── Classes
│   ├── Functions
│   ├── base
│   └── language
├── modules                         -> LANSuite modules
├── tests                           -> Unit and integration tests
├── vendor                          -> External dependencies installed by composer
└── website                         -> LANSuite documentation website
```
