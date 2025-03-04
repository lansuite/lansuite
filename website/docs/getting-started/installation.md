---
id: installation
title: Installation
sidebar_position: 2
---

# LanSuite Release

Sadly there is no current official release, we are on that!
!DO NOT USE THE V4.2 Release from 2015!

# LanSuite development branch

## Preparation

### Create build environment
First of all you need to take an inventory of the tools at your disposal.
Following tools are relevant:
 * A machine with a configured web- and database server fulfilling the requirements as listed on [the requirements page](requirements.md)
 * A PHP environment with Composer for local/remote installations (here guides for [Windows](https://www.thecodedeveloper.com/install-composer-windows-xampp/) and [Linux](https://getcomposer.org/download/))
 * [Docker Community Edition](https://www.docker.com/community-edition) to run the container
 * Git (recommended, either as command-line or GUI)
It is required that you have at least a PHP environment with composer available if you do not want to run the Docker container.

### Fetch source code
In any case you will need to fetch the latest version from GitHub.
The recommended way would be to use Git to obtain a working copy by running `git clone https://github.com/lansuite/lansuite.git` in a command line within the working folder.
This is the recommendation as it simplifies later updates.

Alternatively a snapshot of the current source code branch can be downloaded from the project page(e.g. https://github.com/lansuite/lansuite/archive/master.zip for the master).
This must be extracted before usage (e.g. with `unzip master.zip`(Linux) or with a tool like WinZip,WinRAR or 7zip(Windows).

### Prepare to run
After preparing a build environment and obaining the sources you must run a few steps to prepare the solution for deployment.
Open a command line window, browse via `cd <path>` to the folder prepared and execute `composer install --no-dev --optimize-autoloader`to prepare all dependencies.
If you are on a Linux system, you may also need to fix access rights by runnning the following:
```
$ chmod a+rw ./inc/base/config.php
$ chmod -R a+rwX ./ext_inc/
```
In that case it may also be advised to correct file ownership of the folder to the webserver user and group by running `chown -R <user>:<group> .` additionally.

### Upload to target - if needed
*If you have no direct access to the command line on the target system or no way to run the required tools there*

If you had to run the steps above on a different system then you'll need to upload the prepared code to your target
Use a (S)FTP-client then to upload the whole folder to your webspace.
Ensure that file permissions are properly set so that the webserver user is able to write both to the file `/inc/base/config.php` and the folder `./ext_inc/` and it's contents.

### Create Database and databaase user

If not provided you need to create a database user and database for LanSuite to store its data in.
While it is possible to share a database with a different application it is not recommended to do so, for both usability and security reasons.
The database user should only have privileges on the lansuite database and should only be able to connect from the webserver host, in most cases "localhost".
At the current point in time there is no separation between installation/update privileges and privileges for normal operation.
Thus all privilegs on the neew database (and only there!) need to be granted to the lansuite database user.

Native SQL-Statements as reference:
```
CREATE DATABASE lansuite;
CREATE USER 'lansuite'@'localhost' IDENTIFIED BY '<strongpasswordhere>';
GRANT ALL ON lansuite.* TO 'lansuite'@'localhost';
```
It is recommended to do this before continuing with the application installation as the credentials, database and server names will be needed during the Installation Wizzard phase.

## Installation

Once the source code files are ready for use and the database has been set up you can start the installation wizzard by accessing the destination folder.
If you end up with a blank page or a "page not found"-error then check if browsing to  e.g. `lansuite/index.php` works.
In that case the webserver is not configured to serve index.php by default and should be configured accordingly.
If that also does not help then check the server error logs as these will provide a hint at what went wrong.

### Installation Wizzard

Once the intial page loads you should see the guided installation wizzard that should help to create the required database scheme, configure essential settings and a first user.
Once that is done, the installation is ready for use.

## Docker

We assume that you have a running [Docker Community Edition](https://www.docker.com/community-edition) installed.

```
$ git clone https://github.com/lansuite/lansuite.git
$ cd lansuite
$ touch ./inc/base/config.php
$ # Add the content of the example configuration file below into ./inc/base/config.php
$ chmod a+rw ./inc/base/config.php
$ chmod -R a+rwX ./ext_inc/
$ cp docker/mysql.env.dist docker/mysql.env
$ docker-compose up
$ docker-compose run php composer install
```

This will start a [Nginx webserver](https://nginx.org/) with a [php-fpm](https://secure.php.net/manual/en/install.fpm.php) configuration and a [MySQL database](https://www.mysql.com/) for you.
After everything started you should be able to visit http://`<Your-Docker-IP>`:8080/ and see a running LanSuite-System.

*Warning*: This Docker setup should not be used for production. It contains a debugging setup like [Xdebug](https://xdebug.org/).

## Docker with a database dump

If you have already a running website based on LanSuite, you can also start a docker based setup with a copy of your database.
It comes handy to test the new features with your dataset.

This guide assumes that you have already a copy of your database in a single SQL file.
If you don't have one, you can create one with tools like [mysqldump](https://dev.mysql.com/doc/refman/5.7/en/mysqldump-sql-format.html), [PHPMyAdmin](https://www.phpmyadmin.net/) or ask your hoster for a copy.

Move your database dump into the root folder of LanSuite and name it `database-dump.sql`:

```
$ mv /your/db/dump.sql /lansuite/copy/database-dump.sql
```

After this, you can start the [docker-compose](https://docs.docker.com/compose/) setup via

```
$ docker-compose -f docker-compose.yml -f docker-compose.dump.yml up
```

# Common issues

### `ERROR: Couldn't connect to Docker daemon`

> Some distributions (e.g. Fedora) restrict access to the docker daemon socket to user `root` only.
> This results in a error message as `ERROR: Couldn't connect to Docker daemon at http+docker://localunixsocket - is it running?`
> Run the two `docker-compose` commands as user `root` (via `su`or `sudo`) in that case.

### `ERROR: for web  Cannot start service web`

> A different issue exists in Docker for Windows: If you get an error like: `ERROR: for web  Cannot start service web: OCI runtime create failed: container_linux.go:348: starting container process caused "process_linux.go:402: container init caused \"rootfs_linux.go:58: mounting \\\"/c/xampp/htdocs/lansuite/.docker/nginx-development.conf\\\" to rootfs \\\"/mnt/sda1/var/lib/docker/aufs/mnt/a302993ced4b16d16c0ab56c001d97fbdb9742f2f5beff079b18be797e95ff2a\\\" at \\\"/mnt/sda1/var/lib/docker/aufs/mnt/a302993ced4b16d16c0ab56c001d97fbdb9742f2f5beff079b18be797e95ff2a/etc/nginx/conf.d/default.conf\\\" caused \\\"not a directory\\\"\"": unknown: Are you trying to mount a directory onto a file (or vice-versa)? Check if the specified host path exists and is the expected type`
> Then you need to enable drive sharing for the host drive the docker image is stored on as described on [Fix the Host Volume Sharing issue](http://peterjohnlightfoot.com/docker-for-windows-on-hyper-v-fix-the-host-volume-sharing-issue/).

### `ERROR: Version in "./docker-compose.yml" is unsupported. You might be seeing this error because you're using the wrong Compose file version.`

> Your `docker-compose` version is most likely outdated. Currently LanSuite uses the Compose file format v3.0, hence you need at least Compose version 1.10.0 or higher. If you have installed it using your distribution's package manager please uninstall it and follow [the official installation guide](https://docs.docker.com/compose/install/) on how to install a more-recent version.
