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

* PHP 7

## Development

### Coding style guide

This project follows the coding guideline standards:

* [PSR-1: Basic Coding Standard](http://www.php-fig.org/psr/psr-1/)
* [PSR-2: Coding Style Guide](http://www.php-fig.org/psr/psr-2/)