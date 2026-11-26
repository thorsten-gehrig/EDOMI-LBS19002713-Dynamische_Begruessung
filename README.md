# EDOMI LBS 19002713 – Dynamische Begrüßung

EDOMI-Logikbaustein, der dynamische Begrüßungs- und Verabschiedungstexte für
Sprachassistenten erzeugt – abhängig von Tageszeit, Wochenende/Feiertag,
PV-Leistung, Batteriestand, anwesenden Personen und anstehenden Müllabholungen.

* **LBS-Nummer:** 19002713
* **Datei:** `19002713_lbs.php`
* **Ausgabe:** fertiger Sprachtext, der direkt an Alexa bzw. an den
  Sprachausgabe-LBS **19002715** übergeben werden kann.

## Ein- und Ausgänge

| Eingang | Bedeutung |
| ------- | --------- |
| E1 | Trigger: `1` = Begrüßung (Ankunft), `2` = Verabschiedung (Verlassen) |
| E2 | Name der Person (leer → „Unbekannter") |
| E3 | Aktuelle PV-Leistung in Watt – wird bei Ankunft angesagt |
| E4 | Batteriestatus / SoC in Prozent – wird bei Ankunft angesagt |
| E5 | Feiertag/Wochenende (`1` = Ja) – aktiviert entspanntere Begrüßungen |
| E6 | Anzahl Personen anwesend – bei `0` zusätzliche Sätze fürs leere Haus |
| E7 | Mülltonne (`1` = Ja) – Erinnerung beim Verlassen |
| E8 | Gelber Sack (`1` = Ja) – Erinnerung beim Verlassen |
| E9 | Altpapier (`1` = Ja) – Erinnerung beim Verlassen |

| Ausgang | Bedeutung |
| ------- | --------- |
| A1 | Fertiger Sprachtext |

## Logik

**Begrüßung (E1 = 1)**
* Textpool je nach Tageszeit (vor 12 Uhr, 12–18 Uhr, ab 18 Uhr)
* eigener, entspannterer Textpool an Wochenenden und Feiertagen
* optionale Statusansage:
  * PV-Leistung > 2000 W → „Die Sonne liefert gerade kräftige X Kilowatt."
  * sonst Batterie ≥ 80 % → „Dein Speicher ist … prall gefüllt."
  * sonst Batterie ≤ 20 % → Warnung zum niedrigen Speicherstand

**Verabschiedung (E1 = 2)**
* zufälliger Abschiedssatz
* bei 0 anwesenden Personen zusätzlich Sätze zum Abwesenheits-/Energiesparmodus
* Erinnerung an Mülltonne, Gelben Sack und Altpapier (auch kombiniert:
  „Vergiss nicht, die Mülltonne und den Gelben Sack herauszustellen.")

## Installation

1. In EDOMI unter *Logikbausteine → Import* die Datei `19002713_lbs.php`
   importieren (die LBS-Nummer wird beim Import vergeben).
2. Baustein in eine Logikseite ziehen, Eingänge verknüpfen, A1 z. B. mit dem
   Sprachausgabe-Baustein verbinden.
