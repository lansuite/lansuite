# LANSuite - Web based LAN-Party Management System

[![Build Status](https://travis-ci.org/lansuite/lansuite.svg?branch=master)](https://travis-ci.org/lansuite/lansuite)

LANSuite is a administration system for LAN-Parties based.

*German version of this README*: Can be found at [README-DE.md](./README-DE.md).

## Features

* Organisation of tournaments
* Registration for parties
* News- and messaging system
* Projector support
* Cash / Money management
* Foodcenter
* Hardware / Server inventory
* Picture gallery
* Seat plans
* and many more ...

## Requirements

* >= PHP 7 (with `mysqli`, `snmp` and `gd` extensions)
* >= MySQL 5.6.3

## Installation

### Docker

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

### Configuration file

An example configuration file looks like:

```ini
[lansuite]
version=Nightly
default_design=simple
chmod_dir=777
chmod_file=666
debugmode=0

[database]
server=mysql
user=root
passwd=
database=lansuite
prefix=ls_
charset=utf8
```

**Warning**: Setting directories to `0777` is not suggested for production. Only your webserver user should be able to write into this directory.

## Development

### Contribution Guide

Checkout how to contribute in our [Contribution Guide](./CONTRIBUTING.md).

### Language

Main language of this project is english.
We also support issues and pull requests in german.
The reason is that LANSuite is a quite old system and a massive userbase only speaks german.
To not loose them, we will support both languages.
See [Switch language of documentation, development, communication to english #2](https://github.com/lansuite/lansuite/issues/2) for more details.

### Coding style guide

This project follows the coding guideline standards:

* [PSR-1: Basic Coding Standard](http://www.php-fig.org/psr/psr-1/)
* [PSR-2: Coding Style Guide](http://www.php-fig.org/psr/psr-2/)

### Generating API docs

Former versions of LANSuite bundled an API documentation in the `docs/` folder.
To generate an API documentation in a HTML-Version you can use [phpDocumentor](https://www.phpdoc.org/):

```
$ composer install
$ bin/phpdoc run --progressbar -t ./docs/
```
