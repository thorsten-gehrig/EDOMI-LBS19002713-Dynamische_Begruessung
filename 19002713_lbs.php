###[DEF]###
[name		= Dynamische Begruessung v1.0	]

[e#1		= Trigger (1=Komm, 2=Geh, 3=Komm+Status, 4=Geh+Status)	]
[e#2		= Name				]
[e#3		= PV-Leistung (W)		]
[e#4		= Batteriestatus (%)		]
[e#5		= Feiertag (1=Ja)		]
[e#6		= Personen anwesend		]
[e#7		= Muelltonne (1=Ja)		]
[e#8		= Gelber Sack (1=Ja)		]
[e#9		= Altpapier (1=Ja)		]

[a#1		= Sprachtext			]
###[/DEF]###

###[HELP]###
Der Baustein generiert dynamische Begrüßungen und Verabschiedungen für Sprachassistenten.

Version: 1.0

Eingänge:
E1: Trigger (1 = Begrüßung / Ankunft, 2 = Verabschiedung / Verlassen, 3 = Begrüßung mit Status, 4 = Verabschiedung mit Status)
E2: Name der Person (Text). Bleibt der Eingang leer, wird "Unbekannter" verwendet.
E3: Aktuelle PV-Leistung in Watt (Zahl) - Statusansage nur bei E1 = 3. Ab mehr als 1000 W wird die Leistung angesagt: bis 3000 W in Watt, darüber in vollen Kilowatt.
E4: Aktueller Batteriestatus / SoC in Prozent (Zahl) - Statusansage nur bei E1 = 3 und nur, wenn die PV-Leistung nicht bereits angesagt wurde
E5: Feiertag/Wochenende (1 = Ja, 0 = Nein) - aktiviert entspanntere Begrüßungen
E6: Anzahl Personen anwesend (Zahl) - Bei '0' werden beim Verlassen spezielle Sätze für das leere Haus (Energiesparmodus etc.) freigeschaltet.
E7: Mülltonne (1 = Ja, 0 = Nein) - Erinnert bei E1 = 4 an die Mülltonne.
E8: Gelber Sack (1 = Ja, 0 = Nein) - Erinnert bei E1 = 4 an den Gelben Sack.
E9: Altpapier (1 = Ja, 0 = Nein) - Erinnert bei E1 = 4 an das Altpapier.

Hinweis: Die Zusatzinformationen aus E3, E4, E7, E8 und E9 werden nur bei
E1 = 3 (Komm mit Status) bzw. E1 = 4 (Geh mit Status) ausgewertet. Bei
E1 = 1 und E1 = 2 entfällt der Statusteil, es wird nur der Begrüßungs- bzw.
Verabschiedungstext ausgegeben.

Ausgänge:
A1: Fertiger Sprachtext (kann direkt an Alexa / LBS 19002715 übergeben werden)
###[/HELP]###

###[LBS]###
<?
/**
 * EDOMI LBS 19002713 - Dynamische Begruessung
 * Version 1.0
 *
 * Copyright (C) 2026 Thorsten Gehrig
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, version 3 of the License.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 *
 * SPDX-License-Identifier: LGPL-3.0-only
 */
function LB_LBSID($id) {
    $E = getLogicEingangDataAll($id);
    
    $trigger = (int)$E[1]['value'];
    
    if ($E[1]['refresh'] == 1 && $trigger != 0) {
        
        $name = $E[2]['value'];
        if (trim($name) == "") {
            $name = "Unbekannter";
        }
        
        $pv_power = (int)$E[3]['value'];
        $battery_soc = (int)$E[4]['value'];
        $is_holiday = ($E[5]['value'] == 1) ? true : false;
        $persons_present = (int)$E[6]['value'];
        
        $trash_muell = (int)$E[7]['value'];
        $trash_gelb = (int)$E[8]['value'];
        $trash_papier = (int)$E[9]['value'];
        
        $final_text = "";
        
        // --- 1. LOGIK FÜR BEGRÜSSUNG (Ankunft: 1 = ohne Status, 3 = mit Status) ---
        if ($trigger == 1 || $trigger == 3) {
            $hour = (int)date('H');
            $day_of_week = (int)date('N'); 
            $is_weekend = ($day_of_week >= 6) ? true : false;
            $relaxed_mode = ($is_weekend || $is_holiday);
            
            $greetings = array();
            
            if ($hour < 12) {
                if ($relaxed_mode) {
                    $greetings = array(
                        "Guten Morgen, $name! Genieß deinen freien Tag.",
                        "Ausschlafen tut gut. Schönen guten Morgen, $name!",
                        "Moin $name. Mach dir einen ganz entspannten Morgen.",
                        "Hallo $name, willkommen im entspannten Teil der Woche.",
                        "Guten Morgen, $name. Der Kaffee schmeckt an freien Tagen doch am besten."
                    );
                } else {
                    $greetings = array(
                        "Guten Morgen, $name! Willkommen zurück.",
                        "Moin $name. Der Kaffee wartet schon.",
                        "Guten Morgen! Startklar für den restlichen Tag, $name?",
                        "Hallo $name, ich hoffe, du hattest einen guten Start in den Tag.",
                        "Willkommen daheim, $name. Der Vormittag ist noch jung.",
                        "Schönen guten Morgen, $name. Alles läuft nach Plan.",
                        "Hey $name! Schön, dich heute Morgen schon zu sehen.",
                        "Guten Morgen, $name. Mach es dir gemütlich.",
                        "Hallo $name! Bereit für die nächsten Aufgaben?",
                        "Moin $name! Lass uns den Tag entspannt angehen."
                    );
                }
            } elseif ($hour < 18) {
                if ($relaxed_mode) {
                    $greetings = array(
                        "Schönen Nachmittag, $name! Genieß deine freie Zeit.",
                        "Hallo $name. Zeit zum Relaxen an deinem freien Tag.",
                        "Willkommen zurück, $name. Leg die Beine hoch.",
                        "Hey $name, entspann dich und lass es dir gut gehen.",
                        "Guten Tag, $name. Keine Hektik heute, nur Entspannung."
                    );
                } else {
                    $greetings = array(
                        "Guten Tag, $name. Schön, dass du da bist.",
                        "Willkommen zurück, $name. Zeit für eine kleine Pause.",
                        "Hey $name! Willkommen daheim.",
                        "Hallo $name, ich hoffe, dein Tag war bisher erfolgreich.",
                        "Schönen Nachmittag, $name. Mach dir eine gute Zeit.",
                        "Willkommen, $name! Das Haus ist perfekt temperiert.",
                        "Guten Tag, $name. Endspurt für heute!",
                        "Hallo $name! Zeit für einen Kaffee?",
                        "Hey $name, schön dich am Nachmittag hier zu haben.",
                        "Willkommen zurück, $name. Lass die Seele baumeln."
                    );
                }
            } else {
                if ($relaxed_mode) {
                    $greetings = array(
                        "Guten Abend, $name. Ein entspannter freier Tag neigt sich dem Ende.",
                        "Willkommen zurück, $name. Genieße den restlichen freien Abend.",
                        "Hallo $name. Mach's dir auf dem Sofa bequem.",
                        "Schönen Abend, $name! Lass den freien Tag gemütlich ausklingen.",
                        "Nabend $name. Zeit für Unterhaltung und Entspannung."
                    );
                } else {
                    $greetings = array(
                        "Guten Abend, $name. Willkommen im Feierabend.",
                        "Hallo $name, mach's dir gemütlich für heute.",
                        "Willkommen zurück, $name. Die Systeme schalten auf Feierabend-Modus.",
                        "Schönen guten Abend, $name. Zeit zum Entspannen.",
                        "Hey $name! Der stressige Teil des Tages liegt hinter dir.",
                        "Guten Abend, $name. Das Smart Home wünscht einen ruhigen Ausklang.",
                        "Hallo $name. Leg die Füße hoch, du hast es dir verdient.",
                        "Willkommen zurück, $name. Die Beleuchtung ist bereits gedimmt.",
                        "Nabend $name! Schön, dass du wohlbehalten zurück bist.",
                        "Guten Abend, $name. Lass uns den Tag entspannt beenden."
                    );
                }
            }
            
            $greeting_text = $greetings[array_rand($greetings)];
            
            // Status aus E3/E4 nur bei Trigger 3 (Komm mit Status) ansagen
            $status_text = "";
            if ($trigger == 3) {
                if ($pv_power > 3000) {
                    $kw = (int)round($pv_power / 1000);
                    $status_text = " Die Sonne liefert gerade kräftige $kw Kilowatt.";
                } elseif ($pv_power > 1000) {
                    $status_text = " Die Sonne liefert gerade $pv_power Watt.";
                } elseif ($battery_soc >= 80) {
                    $status_text = " Dein Speicher ist mit $battery_soc Prozent prall gefüllt.";
                } elseif ($battery_soc <= 20) {
                    $status_text = " Achtung, dein Heimspeicher ist nur noch bei $battery_soc Prozent.";
                }
            }
            
            $final_text = $greeting_text . $status_text;
            
        // --- 2. LOGIK FÜR VERABSCHIEDUNG (Verlassen: 2 = ohne Status, 4 = mit Status) ---
        } elseif ($trigger == 2 || $trigger == 4) {
            $farewells = array(
                "Tschüss $name, bis später!",
                "Mach's gut $name! Ich halte hier die Stellung.",
                "Einen schönen und erfolgreichen Tag noch, $name!",
                "Bis bald, $name. Ich passe auf das Haus auf.",
                "Gute Wege, $name, und komm sicher wieder.",
                "Viel Spaß unterwegs, $name!",
                "Mach es gut, $name. Komm bald wieder!"
            );
            
            // Wenn niemand mehr im Haus ist, füge die Abwesenheits-Texte zum Zufallspool hinzu
            if ($persons_present == 0) {
                $farewells_empty = array(
                    "Tschüss $name! Alle Systeme schalten auf Abwesenheit.",
                    "Wir sehen uns, $name! Der Energiesparmodus ist jetzt aktiv.",
                    "Bis später, $name. Licht und Heizung sind heruntergefahren."
                );
                $farewells = array_merge($farewells, $farewells_empty);
            }
            
            $farewell_text = $farewells[array_rand($farewells)];
            
            // Müll-Erinnerung aus E7/E8/E9 nur bei Trigger 4 (Geh mit Status)
            $trash_text = "";
            if ($trigger == 4) {
                $trash_items = array();
                if ($trash_muell == 1) { $trash_items[] = "die Mülltonne"; }
                if ($trash_gelb == 1) { $trash_items[] = "den Gelben Sack"; }
                if ($trash_papier == 1) { $trash_items[] = "das Altpapier"; }

                if (count($trash_items) > 0) {
                    $last_item = array_pop($trash_items);
                    if (count($trash_items) > 0) {
                        $trash_string = implode(", ", $trash_items) . " und " . $last_item;
                    } else {
                        $trash_string = $last_item;
                    }
                    $trash_text = " Vergiss nicht, $trash_string herauszustellen.";
                }
            }
            
            $final_text = $farewell_text . $trash_text;
        }
        
        // --- 3. AUSGABE ---
        if ($final_text != "") {
            setLogicLinkAusgang($id, 1, $final_text);
        }
    }
}
?>
###[/LBS]###

###[EXEC]###
<?
?>
###[/EXEC]###
