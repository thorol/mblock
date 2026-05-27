---
name: mblock-dev
description: MBlock Addon development skill. Use when working on FriendsOfREDAXO mblock, especially compatibility fixes, MForm integration, widget reindexing, copy/paste, online/offline logic, docs, changelog, README, API documentation, or code-quality checks.
---

# MBlock Development Skill

Du arbeitest im REDAXO-Addon MBlock.

Dieses Addon ist funktional stabil und befindet sich im Wartungsmodus. Arbeite konservativ, kompatibel und dokumentationsbewusst.

## Primäre Ziele

- Bestehende MBlock-Installationen stabil halten
- Rückwärtskompatibilität für Module und Inhalte bewahren
- MForm-Kompatibilität erhalten oder verbessern
- Editor- und Widget-Stabilität sichern
- Codeänderungen immer mit Qualitätschecks und Doku-Pflege abschließen

## Wo du was findest

### Einstieg und Metadaten

- `package.yml` – Version, REDAXO-Anforderung, Backend-Seiten, Default-Konfiguration
- `boot.php` – Asset-Laden, Extension-Registrierung, SortableJS-Auswahl, Backend-Initialisierung
- `install.php` / `update.php` – Installations- und Update-Verhalten

### Öffentliche Dokumentation

- `README.md` – öffentliche Hauptdoku, Maintenance-Status, Feature-Übersicht, MForm-Kompatibilität
- `CHANGELOG.md` – Release-Historie
- `API.md` – API-Dokumentation
- `docs/tutorial.md` – Tutorial/Backend-Doku

### Kerncode

- `lib/MBlock/` – Kernlogik des Addons
- `lib/MBlock/Processor/` – Verarbeitung rund um Speichern, Reindexing, Nachbereitung
- `lib/MBlock/Utils/` – Hilfsklassen und Supportlogik
- `lib/deprecated/` – Legacy-Kompatibilität berücksichtigen, nicht leichtfertig entfernen
- `lib/yform/` – YForm-Integration

### Frontend-/Backend-Assets

- `assets/mblock.js` – JS-Quellcode
- `assets/mblock.min.js` – ausgelieferte Minified-Datei
- `assets/mblock.css` – Styles
- `assets/sortable.min.js` – lokaler Fallback für SortableJS
- `build.js` – Build-/Minify-Skript
- `package.json` – `npm run build`

### Projektnahe Hinweise

- `tests/CKEditor5_restore.md` – editorbezogene Hinweise/Testwissen

## Was besonders sensibel ist

### Rückwärtskompatibilität

Ändere diese Bereiche nur sehr vorsichtig:

- Datenstruktur der gespeicherten Blockinhalte
- Öffentliche API-Methoden von `MBlock`
- Legacy-Nutzung ohne Namespace
- DOM-Strukturen, auf die bestehendes JS und Templates angewiesen sind
- Widget-IDs und Popup-Callbacks von REDAXO-Widgets

### Kritische Features

- Copy/Paste von Blöcken
- Online/Offline-Toggle
- Reindexing nach Add, Sort, Delete und Paste
- MForm-Felder in MBlock
- TinyMCE-, CKEditor- und markitup-Verhalten
- SortableJS-Fallback über `bloecks` oder lokale Assets

## Bevorzugte Arbeitsweise

1. Identifiziere zuerst den kleinsten kontrollierenden Codepfad.
2. Ändere nur den betroffenen Bereich.
3. Prüfe danach sofort den engsten möglichen Check.
4. Aktualisiere anschließend die begleitende Dokumentation.

## Kompatibilitätsregeln

- Keine stillen Breaking Changes an gespeicherten Inhalten
- Keine unnötigen Änderungen an Methodensignaturen oder Rückgabeformaten
- Bestehende Konfigurationswerte und Default-Verhalten respektieren
- Bei MForm-Integration immer davon ausgehen, dass bestehende Module bereits produktiv genutzt werden
- Wenn du ein historisches Verhalten anfasst, dokumentiere es im Changelog und in der README/API-Doku, falls Nutzer es bemerken könnten

## Qualitätschecks

### PHP / REDAXO

Nach relevanten PHP-Änderungen immer statische Analyse ausführen:

```bash
docker exec -it coreweb bash -c "cd /var/www/html/public && php redaxo/bin/console rexstan:analyze redaxo/src/addons/mblock/"
```

Wenn nur ein enger Bereich betroffen ist, kann auch zuerst ein Teilpfad geprüft werden, am Ende aber das Addon insgesamt mitdenken.

### JavaScript

Wenn `assets/mblock.js` geändert wurde:

```bash
cd public/redaxo/src/addons/mblock
npm run build
```

Danach sicherstellen, dass `assets/mblock.min.js` zum Quellstand passt.

## Dokumentationspflicht

Bei veränderten Features, APIs, Kompatibilitätsregeln, Editor-Hinweisen, Build-Abläufen oder Nutzerverhalten immer diese Dateien prüfen und bei Bedarf aktualisieren:

- `CHANGELOG.md`
- `README.md`
- `API.md`
- `docs/tutorial.md`

## Was in den jeweiligen Dateien gepflegt wird

### `CHANGELOG.md`

- Release-taugliche Formulierungen
- Bugfixes, Kompatibilitätsfolgen, relevante Stabilitätsverbesserungen
- Keine internen Arbeitsnotizen

### `README.md`

- Öffentliche Feature-Übersicht
- Maintenance-Status
- MForm-Kompatibilität
- Installations- und API-Einstieg

### `API.md`

- Öffentliche Methoden und Beispiele
- Verhalten von Filter-, Sortier-, Gruppier- und Limit-Helfern

### `docs/tutorial.md`

- Anwenderorientierte Nutzung, typische Patterns, Einschränkungen

## Technische Leitplanken

- REDAXO-Core-APIs bevorzugen statt eigener Hilfskonstrukte
- Bestehende Patterns im Addon wiederverwenden
- Keine breiten Refactorings ohne klaren Nutzen
- Bei Änderungen an Reindexing oder Widgets immer die Folgekette mitdenken: HTML, IDs, JS-Selektoren, Popup-Handler, gespeicherte Werte

## Entscheidungsregel bei Unsicherheit

Wenn eine Änderung zwischen Modernisierung und Kompatibilität abwägen muss, entscheide standardmäßig für Kompatibilität.