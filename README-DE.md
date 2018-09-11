# LANSuite - Web based LAN-Party Management System

[![Build Status](https://travis-ci.org/lansuite/lansuite.svg?branch=master)](https://travis-ci.org/lansuite/lansuite)

LANSuite ist ein Administrationssystem für LAN-Partys.

*Englische Version der README*: Kann unter [README.md](./README.md) gefunden werden.

## Features

* Organisation von Turnieren
* Registrierung von und für LAN-Partys
* News- und Nachrichten-System
* Support für Beamer
* Geld / Budget Organisation
* Organisation von Essen
* Hardware / Server inventory
* Bildergalerie
* Sitzpläne
* und viele weitere ...

## Requirements

* PHP 7 (mit den Extensions `mysqli`, `snmp` und `gd`)

## Installation

### Docker

Wir setzen eine einsatzbereite [Docker Community Edition](https://www.docker.com/community-edition) voraus.

```
$ git clone https://github.com/lansuite/lansuite.git
$ cd lansuite
$ touch ./inc/base/config.php
$ # Füge den Inhalt der Beispiel-Konfiguration (siehe unten) in ./inc/base/config.php ein 
$ chmod 0777 ./inc/base/config.php
$ chmod -R 0777 ./ext_inc/
$ docker-compose up
$ docker-compose run php composer install
```

Hinweis: 
Einige Distributionen (e.g. Fedora) erlauben nur dem Benutzer `root` Zugriff auf den Socket für den Docker Daemon.
Dies resultiert in einer Fehlermeldung wie `ERROR: Couldn't connect to Docker daemon at http+docker://localunixsocket - is it running?` 
In diesem Fall  müssen die beiden `docker-compose` Befehle als Benutzer `root` (über `su` oder `sudo`) ausgeführt werden.

Die Befehlsreihenfolge startet nun einen [Nginx webserver](https://nginx.org/) mit einer [php-fpm](https://secure.php.net/manual/en/install.fpm.php) Konfiguration und einer [MySQL Datenbank](https://www.mysql.com/).
Wenn alles gestartet wurde, besuche http://<Your-Docker-IP>:8080/ und du siehst ein Einsatzbereites LANSuite-System.

### Konfigurationsdatei

Eine Beispiel-Konfiguration kann folgendermaßen aussehen:

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

**Warnung**: 
Chmod auf `0777` zu setzen ist nicht für Produktionssysteme zu empfehlen. Nur euer eigener Webserver sollte in Schreibrechte haben.

## Development

### Contribution Guide

Wie du bei diesem Projekt mitwirken kannst, ist in unserem [Contribution Guide](./CONTRIBUTING-DE.md) beschrieben.

### Sprache

Die Hauptsprache des Projektes ist Englisch.
Wir unterstützen Bugs und Pull-Requests auch in deutscher Sprache.
Der Grund ist, dass LANSuite ein altes System ist und viele der bisherigen Nutzer nur Deutsch sprechen.
Um diese Nutzerschaft nicht zu verlieren, supporten wir beide Sprachen.
Weitere Details können dem Ticket [Switch language of documentation, development, communication to english #2](https://github.com/lansuite/lansuite/issues/2) entnommen werden.

### Coding style guide

Das Projekt folgt folgenden coding guideline Standards:

* [PSR-1: Basic Coding Standard](http://www.php-fig.org/psr/psr-1/)
* [PSR-2: Coding Style Guide](http://www.php-fig.org/psr/psr-2/)

### Generierung der API Dokumentation

Vorherige Versionen von LANSuite haben eine API Dokumentation im Ordner `docs/` mitgeliefert.
Um diese API Dokumentation im HTML-Format zu generieren, kann [phpDocumentor](https://www.phpdoc.org/) genutzt werden:

```
$ composer install
$ bin/phpdoc run --progressbar -t ./docs/
```
