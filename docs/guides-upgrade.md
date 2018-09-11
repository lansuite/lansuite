---
id: upgrade
title: Upgrade to a newer version
---

## Foreword

> Please note that this guide is work in progress!
> If there is one step you should follow one by one, then it is the backup of the current system.
> If you find any issues with the guide, then please tell us so (by [opening a new issue on Github](https://github.com/lansuite/lansuite/issues/new))!

## Prerequisites

* A system having both Git and Composer up and running if you want to use the Git Master
* A current file and database backup
* Your target system running PHP >= 7.0 and MySQL >= 5.6.3
* A fair bit of time

## Preparation

### Plan

It is recommended to tell your users (and fellow administrators) in advance that your LanSuite instance won't be 100% functional available during the upgrade.
That should be well done in advance if you run a larger installation, for smaller ones no one may notice. But better be safe than sorry.

### Prepare Upgrade files

#### Official release

* Download the new release
* Extract it on your machine

#### Git Master

* Run `git clone https://github.com/lansuite/lansuite.git`
* Run `composer install`

#### Server Upload

Upload all files into a different folder to your server.
If you executed the previous step already on your server, then obviously nothing to be done at this step.

### Set system to read-only

It is recommended to lock your installation and avoid changes during/after the backup.
This can either be done by either of the following:
* Lock LanSuite by enabling the corresponding option under "Admin-Page" -> "Common Settings" -> "Lock LanSuite page"
* Shut down your web server
* Move index.php to a different location

### Back up the database

Also multiple variants here:
* LS XML export ("Admin-Page" -> "Export" -> "XML - Full Database")
* MySQL commandline dump (Something like `mysqldump --quick -u<ls user> -p <dbname> > exportfile.sql`)
* PHPMyAdmin - if installed
* Any tool provided by your hoster

Backing up your database using `mysqldump` is recommended, as both, LanSuite and PHPMyAdmin, may hit server limits on larger installations.

### Backup the current installation

* If you have command line access: `tar cfvz ls_backup.tar.gz <ls_folder>` or `cp -R <ls_folder> <ls_folder>.backup`
* Download the whole folder with (S)FTP or any other remote access tool you have available

## Upgrade

### Copy over files from an old installation

Things of relevance here:
* The base configuration file from `inc/base/config.php`
* Anything under `ext_inc`
* Custom designs from `designs/`

Copy them to the same place on the new installation, overwriting everything there.

### Check file access rights

Depending on the user you did the installation with it may be required to reset the file and folder access rights.

### Run specific release tasks

It may be required to run additional steps to prepare everything for the new version.
These will be detailed in either `README.md` or `UPGRADE.md`

### Test your installation

Now is the time to look if everything is as expected.
If yes, then you are good to unlock the installation.
If not then either see what broke in the various logs or go to Rollback.

## Rollback (if stuff doesn't work)

In case your upgrade failed terribly, and you need to return to your old state the following needs to be done:

### Import DB backup

You have to reimport the database backup taken before. How this is done varies in what tools you have available.
The command line call for this would be `mysql -u<ls_user> -p < exportfile.sql`

### Restore folder backup

Next step is to restore the folder backup taken.
First clean up (`rm -rf <failed_update_folder>`) or move away (` mv <failed_update_folder> <anyothername>`) the failed installation.
Then restore the original installation from your backup.
Either by extracting (`gunzip ls_backup.tar.gz && tar xvf ls_backup.tar`) or moving back the copied folder (`mv <ls_folder>.backup <ls_folder>`).
Or: Re-uploading your backup, if that was the way you went.

### Unlock installation

As the backup was taken at a point where the installation was locked, it may still be required to unlock it
This would be done by reverting whatever was done to facilitate the original lock

## Pitfalls and Known Bugs

* Please ensure that export and import of Database images use the same character encoding. Using the same client on the same system should ensure this, but be cautious if dump and import are done on different systems/clients
* MySQL 5.7 changes default behavior for GROUP BY clauses. This may lead to unexpected errors in some cases. See [#117](https://github.com/lansuite/lansuite/issues/117) for details
* The master branch is not usable without pulling in additional resources via composer. You must do this first to obtain a runnable installation!
* You have to run a database table upgrade as an extension of the IP field is required to store logging information
* Database character encoding is changed in the default settings from Latin1 to UTF8. Add an configuration entry to `/inc/base/config.php` under the `[database]` section named `charset = "Latin1"` to enforce Latin1 encoding 
