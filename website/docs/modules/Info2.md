---
id: settings
title: Settings
sidebar_position: 1
---

## Module Description

This module provides the abilitity to create and manage multiple rich-text pages for user information pages

## Configuration options

TBD

## Placeholders and replacement values

The following placeholders can be used at the moment in info texts and will be replaced on display with the related values.
The placeholder name will be displayed if the value cannot be resolved

### User-Related
| Variable   | Replacement value         |
|------------|---------------------------|
| %USERID%   | The numeric ID of the user|
| %USERNAME% | The username (nickname) |

## Party Related
| Variable         | Replacement value         |
|------------------|---------------------------|
| %PARTYID%        | The numeric ID of the currently selected party for the user|
| %PARTYPRICEID%   | If the user is already registered for the party then this will reflect the price ID|
| %PARTYPRICETEXT% | The name of the ticket price item given|
| %PARTYPRICEVALUE%| The amount defined for the price item|
