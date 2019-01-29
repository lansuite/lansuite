---
id: installation
title: Installation
---

## Docker

We assume that you have a running [Docker Community Edition](https://www.docker.com/community-edition) installed.

```
$ git clone https://github.com/lansuite/lansuite.git
$ cd lansuite
$ touch ./inc/base/config.php
$ # Add the content of the example configuration file below into ./inc/base/config.php
$ chmod 0777 ./inc/base/config.php
$ chmod -R 0777 ./ext_inc/
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

## Common issues

### `ERROR: Couldn't connect to Docker daemon`

> Some distributions (e.g. Fedora) restrict access to the docker daemon socket to user `root` only.
> This results in a error message as `ERROR: Couldn't connect to Docker daemon at http+docker://localunixsocket - is it running?`
> Run the two `docker-compose` commands as user `root` (via `su`or `sudo`) in that case.

### `ERROR: for web  Cannot start service web`

> A different issue exists in Docker for Windows: If you get an error like: `ERROR: for web  Cannot start service web: OCI runtime create failed: container_linux.go:348: starting container process caused "process_linux.go:402: container init caused \"rootfs_linux.go:58: mounting \\\"/c/xampp/htdocs/lansuite/.docker/nginx-development.conf\\\" to rootfs \\\"/mnt/sda1/var/lib/docker/aufs/mnt/a302993ced4b16d16c0ab56c001d97fbdb9742f2f5beff079b18be797e95ff2a\\\" at \\\"/mnt/sda1/var/lib/docker/aufs/mnt/a302993ced4b16d16c0ab56c001d97fbdb9742f2f5beff079b18be797e95ff2a/etc/nginx/conf.d/default.conf\\\" caused \\\"not a directory\\\"\"": unknown: Are you trying to mount a directory onto a file (or vice-versa)? Check if the specified host path exists and is the expected type`
> Then you need to enable drive sharing for the host drive the docker image is stored on as described on [Fix the Host Volume Sharing issue](http://peterjohnlightfoot.com/docker-for-windows-on-hyper-v-fix-the-host-volume-sharing-issue/).

### `ERROR: Version in "./docker-compose.yml" is unsupported. You might be seeing this error because you're using the wrong Compose file version.`

> Your `docker-compose` version is most likely outdated. Currently LanSuite uses the Compose file format v3.0, hence you need at least Compose version 1.10.0 or higher. If you have installed it using your distribution's package manager please uninstall it and follow [the official installation guide](https://docs.docker.com/compose/install/) on how to install a more-recent version.
