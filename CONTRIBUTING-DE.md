# <a id="contributing"></a> Contributing

LANSuite ist ein open source project und lebt von deinen Ideen sowie deinem Beitrag.

Es gibt viele verschiedene Wege sich an diesem Projekt zu beteiligen. Verbessern der Dokumentation, die Erstellung von Fehler oder Feature-Reports oder schreiben von Code um einen Bug zu fixen oder etwas zu verbessern.

#### Table of Contents

1. [Einleitung](#contributing-intro)
2. [Fork das Projekt](#contributing-fork)
3. [Branches](#contributing-branches)
4. [Commits](#contributing-commits)
5. [Pull Requests](#contributing-pull-requests)

## <a id="contributing-intro"></a> Einleitung

Bitte beachte unsere [offenen reports](https://github.com/lansuite/lansuite/issues) bevor du beginst, zum Projekt beizutragen.

Bevor du anfängst, an LANSuite zu arbeiten, [fork das Projekt](https://help.github.com/articles/fork-a-repo/) in deinen Github Account. Das erlaubt dir alle Freiheiten für Experimente für deine Änderungen.
Wenn deine Änderungen vollständig sind, erstelle einen [pull request](https://help.github.com/articles/using-pull-requests/).
Alle Pull Requests werden gereviewed und gemerged, sofern diese unseren Bestimmungen folgen:

* Änderungen liegen in einem eigenen Branch vor
* Neue Funktionalität sollte durch eigene Tests abgedeckt sein
* Änderungen folgen den existierenden Coding Styles und Standard

In den nächsten Kapiteln beschreiben wir das ganze Vorgehen in einem Step by Step Guide.

## <a id="contributing-fork"></a> Fork das Projekt

[Fork das Projekt](https://help.github.com/articles/fork-a-repo/) in deinen GitHub Account und clone das Repository:

```
git clone https://github.com/andygrunwald/lansuite.git
```

Füge einen neuen Remote `upstream` mit dem Repository als Ziel hinzu.

```
git remote add upstream https://github.com/lansuite/lansuite.git
```

Nun kannst du die letzten Updates zu deinem Fork herunterladen:

```
git fetch --all
git pull upstream HEAD
```

Lese weiter und lerne alles notwendige über [branches](CONTRIBUTING.md#contributing-branches).

## <a id="contributing-branches"></a> Branches

Einen sinnvollen Namen für seinen neuen Branch zu wählen, hilft uns diesen einem Feature oder einem Bug zuzuordnen.

Allgmein sollte ein Branch name ein Thema wie z.B. `fix` oder `feature` gefolgt von einer Beschreibung sowie einer Ticket-Nummer (sofern vorhanden).
Ein einzelner Branch sollte nur Änderungen zu diesem Thema enthalten.

```
git checkout -b fix/service-template-typo-1234
git checkout -b feature/config-handling-1235
```

Lese weiter um deine Changes zu comitten.

## <a id="contributing-commits"></a> Commits

Wenn du deine Änderungen fertig gestellt hast, solltest du diese Comitten.
Eine gute Commit Nachricht enthält ein kurzes Thema, einen Body und eine Referenz zu einem Ticket (sofern vorhanden)

Fixes:

```
Fix problem with notifications in Chrome browser

Chrome has implemented a new JavaScript API for push notifications

refs #4567
```

Features:

```
Add a new projector plugin

On LAN-Parties it is useful to show important information on bigger screens.
The projector module was built for this.

refs #1234
```

Du kannst mehrere Commits für deine Änderungen benutzen um deinen Patch zu vervollständigen.

## <a id="contributing-pull-requests"></a> Pull Requests

Wenn du deine Änderungen comittest hast, solltest du noch deinen lokalen master branch updaten und deinen Änderungsbranch auf den aktuellsten Stand bringen (rebasen), bevor du deinen Pull Request erstellst.

```
git checkout master
git pull upstream HEAD

git checkout fix/notifications
git rebase master
```

Wenn du alle Konflikte gelöst hast, pushe deinen Branch zu deinem remote Repository.
Ggf. ist es notwendig, deinen push zu erzwingen - Benutze dies mit großer Vorsicht!

Neuer branch:
```
git push --set-upstream origin fix/notifications
```

Vorhandener branch:
```
git push -f origin fix/notifications
```

Du kannst entweder das [hub](https://hub.github.com) CLI tool nutzen um einen PR zu erstellen oder navigiere zu unserem [GitHub repository](https://github.com/lansuite/lansuite) und erstelle dort den PR.

Der Pull Request sollte, wie ein Commit auch, ein Thema sowie eine Referenz zu einem Ticket haben (sofern eins existiert).
Das ermöglicht den Entwicklern das Ticket automatisch zu schließen, wenn der Pull Request gemerged wurde.

Wenn du das Tool [hub](https://hub.github.com) nutzt, tippe:

```
hub pull-request

<a telling subject>

fixes #1234
```

Vielen Dank für deine Contribution!

### <a id="contributing-rebase"></a> Rebase a Branch

Wenn du einen Pull Request erstellt hast, welcher nicht gegen den upstream master gerebased wurde, kann es sein, dass die Entwickler dich bitten deinen Pull Request zu rebasen.

Als erstes, fetche und pulle den `upstream` master.

```
git checkout master
git fetch --all
git pull upstream HEAD
```

Danach wechsel zu deinem Arbeits-Branch und starte gegen den master zu rebasen:

```
git checkout fix/notifications
git rebase master
```

Wenn du einen Konflikt bekommst, wird das rebasen automatisch stoppen und dich fragen, die Probleme zu beheben.

```
git status

  both modified: path/to/conflict.cpp
```

Editiere die Datei und suche nach `>>>`.
Fix, teste und speichere den Code wie angefordert.

Fügre die modifzierten Files hinzu und führe das rebasing fort.

```
git add path/to/conflict.cpp
git rebase --continue
```

Wenn du erfolgreich warst, musst du noch deine Änderungen nach Github pushen:

```
git push -f origin fix/notifications
```

Wenn du Angst hast, etwas kaputt zu machen, kannst du auch den Rebase in einem Sicherungsbranch machen und ihn später mit deinem vorherigen Branch ersetzen.

```
git checkout fix/notifications
git checkout -b fix/notifications-rebase

git rebase master

git branch -D fix/notifications
git checkout -b fix/notifications

git push -f origin fix/notifications
```