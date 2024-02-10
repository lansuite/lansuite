---
id: upgrade
title: Upgrade to a newer version
sidebar_position: 1
---

## Prerequisites

* Your target system need to run PHP >= 8.1 and MySQL >= 5.7
* A fair bit of time

## Preparation

### Upgrade communications

It is recommended to tell your users (and fellow administrators) in advance that your LanSuite instance won't be 100% functional available during the upgrade.

That should be well done in advance if you run a larger installation, for smaller ones no one may notice. But better be safe than sorry.

### Download an official release

1. Download the release from [GitHubs Releases page](https://github.com/lansuite/lansuite/releases)
2. Extract the archive on your machine

### Set system to read-only

It is recommended to lock your installation and avoid changes during/after the backup.
This can either be done by either of the following:

* Lock LanSuite by enabling the corresponding option under "Admin-Page" -> "Common Settings" -> "Lock LanSuite page"
* Shut down public access to your web server
* Move index.php to a different location

### Execute a backup

#### Backup the database

Create a full database backup before upgrading.

There are multiple variants available:

* LanSuite XML export ("Admin-Page" -> "Export" -> "XML - Full Database")
* MySQL commandline dump (Something like `mysqldump --quick -u<ls user> -p <dbname> > exportfile.sql`)
* PHPMyAdmin - if installed
* Any tool provided by your hoster

Backing up your database using `mysqldump` is recommended, as both, LanSuite and PHPMyAdmin, may hit server limits on larger installations.

#### Backup your current installation

* If you have command line access: `tar cfvz ls_backup.tar.gz <ls_folder>` or `cp -R <ls_folder> <ls_folder>.backup`
* Download the whole folder with (S)FTP or any other remote access tool you have available

### Server Upload

Upload all files into a different folder to your server.
If you executed the previous step already on your server, then obviously nothing to be done at this step.

## Upgrade

### Copy over files from an old installation

Replace the new files with the old ones.

A few files and locations are unique to your installation.
Those should be kept.
Like:

* The base configuration file from `inc/base/config.php`
* Anything under `ext_inc`
* Custom designs from `designs/`

Copy them to the same place on the new installation, overwriting everything there.

### Run specific release tasks

It may be required to run additional steps to prepare everything for the new version.
Please check the upgrade guide to your specific version below.

### Execute LanSuite upgrade logic

Visit "Admin-Page" -> "Lansuite updaten / reparieren" ->> "Datenbank updaten und verwalten".
This is also available at `http(s)://<your-domain>/index.php?mod=install&action=db`.

### Test your installation

Now is the time to look if everything is as expected.
If yes, then you are good to unlock the installation.
If not then either see what broke in the various logs or go to Rollback.

### Unlock installation

If you locked your installation, you  can now unlock it again.
"Admin-Page" -> "Common Settings" -> "Lock LanSuite page".

## Rollback (if things doesn't work)

In case your upgrade failed, and you need to return to your old state the following needs to be done:

### Import DB backup

You have to reimport the database backup taken before.
How this is done varies in what tools you have available.
The command line call for this would be `mysql -u<ls_user> -p < exportfile.sql`.

### Restore folder backup

Next step is to restore the folder backup taken.
First clean up (`rm -rf <failed_update_folder>`) or move away (`mv <failed_update_folder> <anyothername>`) the failed installation.
Then restore the original installation from your backup.
Either by extracting (`gunzip ls_backup.tar.gz && tar xvf ls_backup.tar`) or moving back the copied folder (`mv <ls_folder>.backup <ls_folder>`).
Or: Re-uploading your backup, if that was the way you went.

## Pitfalls and Known Bugs

* Please ensure that export and import of Database images use the same character encoding. Using the same client on the same system should ensure this, but be cautious if dump and import are done on different systems/clients.
* The git `master` branch is not usable without pulling in additional resources via composer. You must do this first to obtain a runnable installation!

## Upgrade from LanSuite v4.2 to v5.0

### Configuration: `database.sqlmode`

Add the following line to your configuration at `/inc/base/config.php`:

```
[...]
[database]
sqlmode = "NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION"
[...]
```

### Configuration: `database.charset`

Old installations of LanSuite stored data in the database with character collation set to `latin1`.
But LanSuite now uses `utf8mb4` by default.
Thus it is either required to modify database collation or to force character set back to `latin1`.

#### Offline conversion
Changing the collation requires the following steps:
* Full SQL dump of the database (see backup)
* Drop all tables
* Change collation of database to `utf8mb4` (or drop database and recreate)
* Re-import SQL dump with setting collation of the connection / file to `latin1`

#### Online conversion
The following steps in PHPmyAdmin _should_ also work, this has not been confirmed yet:

    Select the database.
    Click the "Operations" tab.
    Under "Collation" section, select the desired collation.
    Click the "Change all tables collations" checkbox.
    A new "Change all tables columns collations" checkbox will appear.
    Click the "Change all tables columns collations" checkbox.
    Click the "Go" button.

#### Force latin1 as workaround
If you want to enforce `latin1` as workaround you need to add the following key to the configuration file located at `/inc/base/config.php`:

```
[...]
[database]
charset = "latin1"
[...]
```

** Do not change collation setting during normal operation and without doing the required conversion, this will cause data to be stored in both formats with no easy way to remediate **


### Configuration: `google_maps_key`

A separate API Key has been introduced for usage of Google Maps. (#887)
Check the bottom of "Admin-Page" -> "Common Settings" where the existing setting for the Analytics ID is located and add/copy the API key to be used for Google Maps API requests
Map display will be nonfunctional until the key is added.

### Fonts in `ext_inc/pdf_fonts`

Delete the following files

* `ext_inc/pdf_fonts/courier.php`
* `ext_inc/pdf_fonts/helvetica.php`
* `ext_inc/pdf_fonts/helveticab.php`
* `ext_inc/pdf_fonts/helveticabi.php`
* `ext_inc/pdf_fonts/helveticai.php`
* `ext_inc/pdf_fonts/symbol.php`
* `ext_inc/pdf_fonts/times.php`
* `ext_inc/pdf_fonts/timesb.php`
* `ext_inc/pdf_fonts/timesbi.php`
* `ext_inc/pdf_fonts/timesi.php`
* `ext_inc/pdf_fonts/zapfdingbats.php`

Delete the following folder

* `ext_inc/pdf_fonts/makefont`

Add the following files from the release package to the `ext_inc/pdf_fonts` folder

* `ext_inc/pdf_fonts/.gitkeep`

### Converting custom fonts

If you have added your own custom fonts into `ext_inc/pdf_fonts`, you need to convert them into the new fpdf format:

1. Go to http://www.fpdf.org/makefont/
2. Choose your custom font file (ttf)
3. Download the generated *.z and *.php file
4. Add the `*.ttf`, `*.z` and `*.php` into `ext_inc/pdf_fonts`
