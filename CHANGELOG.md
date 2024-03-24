# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

Special Note:
The changelog entries for this release might not be complete.
There is a high chance that changes got into the code base that is not reflected in this list.
The last official release happened > 8 years ago (2015).
We could not reconstruct _all_ changes, but we tried our best to make the most out of it.

### Added

- [Development] Added GitHub Issue Template
- [Development] Added contribution guide
- [Development] Introduced static analyzer tools Phan and PHPStan for automatic bug and code flaw detection
- [Development] Introduced PHPUnit, a framework for unit testing
- [Development] Introduced rector, a framework for instant PHP upgrades and automated refactoring - This will help us to modernize our codebase
- [Development] Introduced GitHub Actions as Continuous Integration (CI) system to execute checks like Unit testing, Shell scripts, or the deployment of our documentation
- [Development] Introduced a Dockerfile and docker-compose setup to setup a unified development environment
- [Development] Introduced Dependabot to help us with automatic version upgrades to stay up to date
- [Development] Introduced Composer, a PHP Package manager to make dependency handling to 3rd party libraries easier
- [Development] Introduced PHP_CodeSniffer to ensure a unified coding standard inside our codebase
- [Development] Introduced a Makefile to make standard commands easier and scriptable
- [Development] Introduced an automated way to create a new production release incl., documentation and API docs
- [Development] Added basic Pull Request template
- [Development] Added versioning strategy for dependabot for npm and composer to auto-increase dependency versions if needed
- [Documentation] Introduced an official documentation website at https://lansuite.github.io/lansuite/
- [System] Introduced symfony/error-handler to provide more information in an error case rather than the native PHP errors (only active in debug mode)
- [System] Introduced symfony/cache to easily provide other cache backends like APCu (next to file caching)
- [System] Introduced symfony/http-foundation to unify safe access to superglobals
- [Database] Added the setting to replace the hardcoded database table prefix `lansuite_` with something admin configurable
- [Database] Introduced options to update single module DB structure (#927)
- [Tournament] Added tournament icon for Starcraft II
- [Tournament] Added tournament icon for Counter-Strike: Global Offensive
- [Tournament] Added tournament icon for Rocket League
- [Tournament] Added roles for organization people (Tech/Admin) into tournaments
- [Tournament] Added an overview about when the games happen in tournaments
- [Tournament] Added day of week to tournament timetable (#902)
- [Guestlist] Added Clan Search into Guestlist
- [Usrmgr] Added Clan Search into Usrmgr
- [Usrmgr] Added XMPP support in the Contacts field
- [Discord] Added configurable timeout for JSON data retrieval
- [Installation] Added system checks for OpenSSL and allow_url_fopen
- [Installation] Added database value User@Server into MySQL error message
- [Installation] Added check for incompatible SQL Modes to the first installation page
- [Installation] If there is no `config.php` file available during installation, create it during setup from the default config
- [Discord] Introduced a new module to manage Discord Servers
- [Party] Add information `Gesamt` in the Party box to show how many people can sign up for a party
- [Birthday] New module to show users birthdays
- [Hall of fame] New module to present all tournament winners in a Hall of Fame
- [Server] Added Voice as server type

### Changed

- [System] Raised the requirements of minimum PHP Version to v8.1
- [System] Raised the requirements of minimum MySQL Version to v5.7
- [System] Upgraded smarty/smarty to 4.3.2
- [System] Upgraded setasign/fpdf to 1.8.6
- [System] Made IP fields IPv6 aware
- [System] Use utf8 by default
- [System] Update Google Analytics Integration
- [System] Enabled Google Analytics Integration anonymizeIp feature
- [System] Separated Google API-Keys for Analytics, Maps and Translate into dedicated settings (#887)
- [System] Don't enforce php-snmp; only suggest it (#148)
- [Cron] Show execution state, runtime and error, deactvate after 3 failed executions (#924) 
- [System] Updated jQuery to v3.7.1 and jQuery UI to v1.13.2
- [Database] Set utf8mb4 as the default charset
- [Database] Add default database port to connection string if not configured
- [User] Allow empty birthday field
- [Statistics] Limit lasthiturl to 100 chars as we expect it to fit varchar(100)
- [User] Enforce uniqueness of user & email for registration
- [Boxes/Login] Sign up and Password Recovery are now text links below the login box and not only icons anymore (for better usability)
- [PDF] Usage of core fonts from fpdf instead of `ext_inc/pdf_fonts`
- [Security] Set Cookies HTTP only and protocol aware (https)
- [Server] Changed CPU and RAM Unit of measure from Mega to Giga in Server hardware information
- [Server] Limited visibility for non-Admins to active party
- [Captcha] Replaced ASCII-Captcha with a graphical captcha

### Deprecated

- [Database] Mark the old Database class (without prepared statement support) as deprecated

### Removed

- [Usrmgr] Removed the WWCL/NGL Search in Usrmgr
- [Teamspeak2] Removed module Teamspeak2
- [System] Removed old IE 7/8 compatibility tags - Dropping support for IE7 and IE8
- [System] Removed dependency to proxy `62.67.200.4`
- [System] Removed PHP Setting `safe_mode` and basic server statistics
- [System] Remove `ini_set('url_rewriter.tags', '')`
- [System] Remove hardcoded set date_default_timezone_set to Europe/Berlin
- [System] Removed mysql_ functionality in favor of mysqli
- [System] Remove nofollow.php from index root
- [Installation] Remove install check "magic_quotes_gpc"
- [Installation] Remove install check "register_globals"
- [Installation] Remove install check "register_argc_argv"
- [Server2] Removed module server2
- [Equipment] Removed module equipment because http://www.orgapage.net/ is not available anymore
- [Captcha] Removed ext_scripts/captcha.php, because it was not in use
- [User] Removed ICQ field in user data
- [User] Removed MSN field in user data
- [Tournament] Removed support for WWCL (WWCL shutdown in 2013)

### Fixed

- [Foodcenter] Fixed the display of prices in Foodcenter by using the data type decimal instead of float
- [System] Fixed various bugs rooted by typos in variable names
- [System] Fixed several PHP syntax errors in the code base
- [System] Fixed many typos in translations (mostly in German language)
- [System] Remove LanSurfer-related code
- [Party] Fixed start page party statistics
- [Cron2] Fixed reserved keyword in newer MySQL versions (keyword 'function') (#482)
- [Cron2] Fixed blank page on failure to execute php / sql (#924)
- [Installation] Skip cache for installation module
- [Installation] Fixed #344: Switching to English during Installation breaks the Installation page (#650)
- [Installation] Fixed dropdown to select the design
- [Installation] Fixed Database error when going from step 2 to 1 in install wizzard (#871)
- [Home] Set module `home` as default if we don't have a module parameter
- [Clanmgr] Only prefix clan URL with http:// if an URL was entered
- [Clanmgr] Only prefix URL field with http:// for display when an URL exists
- [Usermgr] Don't show empty links for clans without URL
- [Mail] Show the proper sender name for internal system messages in the new mailbox
- [Mail] Properly detect and set protocol and port for email links
- [Statistics] Fix display of zero value when no page hits within the last hour
- [Statistics] Avoid division by zero during stats calculation
- [Party] Fix the description of buttons Delete is not edit. The price modification button (icon) had no description at all.
- [Tournament] Tournament overview is not displayed if played as a league (#142)
- [Tournament] MySQL Warning in module tournament2 if the user is not logged in (#97)
- [Guestlist] Restrict user information shown on Google Maps according to their settings and never show the street details ... unless you are an administrator
- [Board] Deletion of board failed without error (#861)

### Security

- [Database] Introduced a new Database class with first-class prepared statement support
- [System] Protect module inclusion for path traversal

## [4.2] - 2015-03-15

No changelog exists for the v4.2 or prior release.
The `CHANGELOG.md` was created after the v4.2 release was created.
It will be maintained from > v4.2 onwards.

[unreleased]: https://github.com/olivierlacan/keep-a-changelog/compare/v4.2...HEAD
[4.2]: https://github.com/lansuite/lansuite/releases/tag/v4.2
