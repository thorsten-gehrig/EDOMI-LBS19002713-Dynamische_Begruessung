# EDOMI LBS 19002713 – Dynamische Begrüßung

EDOMI-Logikbaustein, der dynamische Begrüßungs- und Verabschiedungstexte für
Sprachassistenten erzeugt – abhängig von Tageszeit, Wochenende/Feiertag,
PV-Leistung, Batteriestand, anwesenden Personen und anstehenden Müllabholungen.

* **LBS-Nummer:** 19002713
* **Version:** 1.0
* **Datei:** `19002713_lbs.php`
* **Ausgabe:** fertiger Sprachtext, der direkt an Alexa bzw. an den
  Sprachausgabe-LBS **19002715** übergeben werden kann.

## Ein- und Ausgänge

| Eingang | Bedeutung |
| ------- | --------- |
| E1 | Trigger: `1` = Begrüßung (Komm), `2` = Verabschiedung (Geh), `3` = Begrüßung **mit Status**, `4` = Verabschiedung **mit Status** |
| E2 | Name der Person (leer → „Unbekannter") |
| E3 | Aktuelle PV-Leistung in Watt – Statusansage nur bei E1 = 3 (> 1000 W: bis 3000 W in Watt, darüber in vollen Kilowatt) |
| E4 | Batteriestatus / SoC in Prozent – Statusansage nur bei E1 = 3 |
| E5 | Feiertag/Wochenende (`1` = Ja) – aktiviert entspanntere Begrüßungen |
| E6 | Anzahl Personen anwesend – bei `0` zusätzliche Sätze fürs leere Haus |
| E7 | Mülltonne (`1` = Ja) – Erinnerung nur bei E1 = 4 |
| E8 | Gelber Sack (`1` = Ja) – Erinnerung nur bei E1 = 4 |
| E9 | Altpapier (`1` = Ja) – Erinnerung nur bei E1 = 4 |

| Ausgang | Bedeutung |
| ------- | --------- |
| A1 | Fertiger Sprachtext |

**Wichtig:** Die Zusatzinformationen aus E3, E4, E7, E8 und E9 werden
ausschließlich bei `E1 = 3` (Komm mit Status) bzw. `E1 = 4` (Geh mit Status)
ausgewertet. Bei `E1 = 1` und `E1 = 2` wird nur der reine Begrüßungs- bzw.
Verabschiedungstext ausgegeben. So lassen sich zwei KOs an den Baustein hängen:
ein „stiller" Trigger für die reine Anwesenheitsmeldung und ein Status-Trigger
mit allen Zusatzinfos.

## Logik

**Begrüßung (E1 = 1 oder 3)**
* Textpool je nach Tageszeit (vor 12 Uhr, 12–18 Uhr, ab 18 Uhr)
* eigener, entspannterer Textpool an Wochenenden und Feiertagen
* Statusansage nur bei E1 = 3:
  * PV-Leistung > 1000 W → Ansage der Leistung:
    * bis 3000 W in Watt (z. B. 2450 W → „Die Sonne liefert gerade 2450 Watt.")
    * über 3000 W in vollen Kilowatt (z. B. 3450 W → „… kräftige 3 Kilowatt.")
  * sonst Batterie ≥ 80 % → „Dein Speicher ist … prall gefüllt."
  * sonst Batterie ≤ 20 % → Warnung zum niedrigen Speicherstand

**Verabschiedung (E1 = 2 oder 4)**
* zufälliger Abschiedssatz
* bei 0 anwesenden Personen zusätzlich Sätze zum Abwesenheits-/Energiesparmodus
* Müll-Erinnerung nur bei E1 = 4, auch kombiniert:
  „Vergiss nicht, die Mülltonne und den Gelben Sack herauszustellen."

## Installation

1. In EDOMI unter *Logikbausteine → Import* die Datei `19002713_lbs.php`
   importieren (die LBS-Nummer wird beim Import vergeben).
2. Baustein in eine Logikseite ziehen, Eingänge verknüpfen, A1 z. B. mit dem
   Sprachausgabe-Baustein verbinden.

## Changelog

* **1.0** – Erste veröffentlichte Version: Trigger 1–4 (Komm/Geh, jeweils mit
  und ohne Status), Tageszeit- und Wochenend-/Feiertagsbegrüßungen,
  PV-/Batterie-Statusansage bei E1 = 3, Müll-Erinnerung bei E1 = 4.
