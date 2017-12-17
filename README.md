# Lansuite - Webbased LAN-Party Management System

Lansuite ist ein Lanparty-Administrationssystem, basierend auf PHP und MySQL.
Es greift den Organisatoren von Lanparties in vielen Bereichen wie Turnierorganistation, Anmeldung oder Nachrichtenverwaltung unter die Arme und ermöglicht so, dass sich die Organisatoren auf die wesentlichen Dinge Ihrer Party konzentieren können.

Auch für die Partybesucher bietet Lansuite eine Vielzahl von Möglichkeiten, am wichtigsten dürften hier die Kommunikationskomponenten von Lansuite, wie der ICQ-ähnliche Messenger und die Boards sein, aber auch die Möglichkeit sich über aktuelle Geschehnisse in den News zu Informieren, sowie den aktuellen Stand der Turniere zu sehen.

In den Turnierbereichen von Lansuite können die Gäste bequem ihre Spielergebnisse melden und mit dem integrierten Sitzplan behalten sowohl Organisatoren als auch Gäste immer den vollen Überblick über die Lanparty.

Lansuite ist vollständig Lansurfer-kompatibel und unterstützt die Austragung von WWCL-, NGL- und LGZ-Turnieren.

Durch die Verwendung von PHP/MySQL kann Lansuite auf jedem beliebigen System, auf dem ein aktueller Browser installiert ist, benutzt werden, sowie mit fast allen Hosting-Angeboten, die Scriptsprache und Datenbank anbieten im Internet betrieben werden.

Serverseitig können sowohl Linux/Unix- als auch Windows- und Mac-Systeme eingesetzt werden.

## Dies sind die Ziele auf die bei der Entwicklung von Lansuite besonders geachtet wird:

- **Einfache Bedienung, Benutzerfreundlichkeit mit möglichst einfachen Abläufen**

Dieses Ziel stellt die größte Veränderung zu Lansuite Version 1 da. Viele der Konzepte wurden über Bord geworfen, da deren Bedienung zu komplex war. So wurde z.B. das Turniersystem radikal verändert und von unnötigem Ballast befreit. Die einfache Bedienbarkeit und das Learning-by-doing sind wichtige Entwicklungsziele der Version 2. Auch die "unterschwellige" und "leise" Unterstützung des Benutzers war uns wichtig, so sind z.B. alle Tabellen sortierbar usw.

- **Mächtiges Verwaltungswerkzeug für die Organisatoren, das dennoch übersichtlich und einfach zu bedienen ist**

"Nehmt die Orgas an die Hand und gebt ihnen Sicherheit, denn sie haben schon genug zu tun". Getreu diesem Motto wurden die Verwaltungswerkzeuge von Lansuite entwickelt. Da wir selbst Erfahrung im Organisieren von Lanparties haben, wissen wir wo es Probleme gibt und wie kritische Situationen (Anmeldung) gelöst werden können. Lansuite versucht eben diese Situation so einfach wie möglich zu gestalten, insbesondere beim Einchecken der Gäste zu Beginn der Party.

- **Auswahl zwischen unterschiedlichen Designs, möglichst einfache Anpassung/Erstellung von Designs durch Organisatoren**

Das optische Erscheinungsbild von Lansuite kann komplett geändert werden. Mit Lansuite werden mehrere Designs geliefert, zwischen denen der User wählen kann. Wir haben uns für ein Template-Konzept entschieden, da damit die Organisatoren recht einfach eigene Designs erstellen, oder die Standarddesigns anpassen können. Eine Dokumenation dazu ist ebenfalls enthalten.

- **Erhöhung der Sicherheit**

Wenn der Webserver es unterstützt, was wir ausdrücklich empfehlen, arbeitet Lansuite komplett SSL-verschlüsselt. Durch einige Sicherheitskonzepte im Session-Tracking ist ein Session-Hijacking und somit das Ergaunern von Passwörtern auch ohne SSL-Support fast unmöglich. Zusammen mit SSL haben Hacker / Cracker kaum Chancen Passwörter zu knacken. Auch in der Lansuite-Datenbank werden sensible Daten nur verschlüsselt gespeichert.

- **Kleine Hilfebuttons (Helpletts) die zu fast jeder Situation eine on-line Hilfe bieten**

Lansuite bietet zu vielen Fachwörtern kleine Popup-Fenster mit deren Erklärung an. Dieses Prinzip wird konseqent angewendet und beantwortet so die meisten Fragen. Zu wichtigen Bereichen ist außerdem eine Onlinehilfe verfügbar.

- **Gute, ausführliche Dokumentation**

Sollten Onlinehilfe und Helpletts nicht weiterhelfen, kann der Anwender im Wiki blättern. Für die Organisatoren und Entwickler gibt es dort sehr nützliche Hinweise und Tipps.

## Requirements

* >= PHP 7.0 (with mysqli, snmp and gd extensions)

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
```

This will start a [Nginx webserver](https://nginx.org/) with a [php-fpm](https://secure.php.net/manual/en/install.fpm.php) configuration and a [MySQL database](https://www.mysql.com/) for you.
After everythign started you should be able to visit http://<Your-Docker-IP>:8080/ and see a running LanSuite-System.

### Example configuration file

An example configuration file:

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

**Warning**: Setting directories to 0777 is not suggested for production. Only your webserver user should be able to write into this directory.

## Development

### Contribution Guide

#### English

How to contribute is described in detail in our [Contribution Guide](./CONTRIBUTING.md).

#### German / Deutsch

Wie man zu diesem Projekt etwas beisteuert ist sehr detailliert in unserem [Contribution Guide](./CONTRIBUTING-DE.md) beschrieben.

### Coding style guide

This project follows the coding guideline standards:

* [PSR-1: Basic Coding Standard](http://www.php-fig.org/psr/psr-1/)
* [PSR-2: Coding Style Guide](http://www.php-fig.org/psr/psr-2/)


### Generating API docs

Former versions of LANSuite bundled an API documentation in the `docs/` folder.
To regenerate an API documentation in a HTML-Version you can use [phpDocumentor](https://www.phpdoc.org/):

```
phpdoc --progressbar -t ./lansuite-apidocs-html/ -d ./lansuite-code/
```
