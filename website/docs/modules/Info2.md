---
id: settings
title: Settings
sidebar_position: 1
---

## Module Description

This module provides the abilitity to create and manage multiple rich-text pages for user information.
Multiple default pages are provided as example / template including generic party information, how to reach the party, participation rules and so on.

## Configuration options

| Option                                | Impact                                                                                     | Default value |
|---------------------------------------|--------------------------------------------------------------------------------------------|---------------|
|Use WYSIWYG-Editor                     | Loads FCKedit for editing of Info pages, raw HTML-Input field will be used otherwise       | Yes           |
|Add new Entries as subentries of info2 | If enabled new & enabled entries will be automatically added as submenu-Item for the Module| Yes           |

## Placeholders and replacement values

The following placeholders can be used at the moment in info texts and will be replaced on display with the related values.
The placeholder name will be displayed if the value cannot be resolved

### User-Related
| Variable   | Replacement value         |
|------------|---------------------------|
| %USERID%   | The numeric ID of the user|
| %USERNAME% | The username (nickname)   |

## Party Related
These values are replaced if a party is selected
| Variable         | Replacement value                                          |
|------------------|------------------------------------------------------------|
| %PARTYID%        | The numeric ID of the currently selected party for the user|
| %PARTYMAME%      | The given name for the party                               |
| %PARTYBEGIN%     | The party start time and date in format hh:mm dd.mm.yyyy   |
| %PARTYEND%       | The party end time and date in format hh:mm dd.mm.yyyy     |
| %PARTYGUESTS%    | The maximum amount of participants                         |
| %PARTYLOCATION%  | The location given for the party                           |

## Entrance Fee related
User must be registered with a price selected for values to be replaced
| Variable         | Replacement value                                                                  |
|------------------|------------------------------------------------------------------------------------|
| %PARTYPRICEID%   | If the user is already registered for the party then this will reflect the price ID|
| %PARTYPRICETEXT% | The name of the ticket price item given                                            |
| %PARTYPRICEVALUE%| The amount defined for the price item                                              |
