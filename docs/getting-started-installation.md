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

> Some distributions (e.g. Fedora) restrict access to the docker daemon socket to user `root` only.
> This results in a error message as `ERROR: Couldn't connect to Docker daemon at http+docker://localunixsocket - is it running?`
Run the two `docker-compose` commands as user `root` (via `su`or `sudo`) in that case.

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