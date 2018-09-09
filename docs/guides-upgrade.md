---
id: upgrade
title: Upgrade to a newer version
---

## Prerequisites
* A system having both Git and Composer up and running
* A current file and database backup
* Your target system running PHP >= 7.0 and MySQL >= 5.6.3
* A fair bit of time

## Preparation
* Do a database dump (to be more detailed)
* Backup current files (to be more detailed)
* Checkout Git master (to be more detailed)
* Run Composer (to be more detailed)
* Inform your users!

## Upgrade
* Copy over files from old version (to be more detailed)
* DB upgrade (to be more detailed)
* Additional changes (to be more detailed)
* Test

## Rollback (if stuff doesn't work)
* Restore DB image (to be more detailed)
* Restore old files (to be more detailed)

## Pitfalls and known Bugs
* Please ensure that export and import of Database images use the same character encoding. Using the same client on the same system should ensure this, but be cautious if dump and import are done on different systems / clients
* MySQL 5.7 changes default behaviour for GROUP BY clauses. This may lead to unexpected errors in some cases. See [#117](https://github.com/lansuite/lansuite/issues/117) for details
* The master branch is not useable without pulling in additional ressources via composer. You must do this first in order to obtain a runnable installation!
* You have to run a database table upgrade as an extension of the IP field is required to store logging information
* Database character encoding is changed in the default settings from Latin1 to UTF8. Add an configuration entry to `/inc/base/config.php` under the `[database]` section named `charset = "Latin1"` to enforce Latin1 encoding 
