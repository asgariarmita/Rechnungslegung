<?php
require("includes/config.inc.php");
require("includes/conn.inc.php");
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Rechungungsdaten</title>
    <style>
        table {
            table-layout: fixed;
            width: 300px;
        }

        th {
            text-align: left;
        }

        td {
            border: 0px solid transparent;
            text-align: left;
        }

        th
    </style>
</head>

<body>
    <h1>SPUTNIK</h1>
    <ul>
        <li><a href="startseite.php">Startseite</a></li>
        <li><a href="filterung.php">Filterung</a></li><br>
    </ul>
    <p>SYNE Marketing & Consulting GmbH • SPUTNIK Trackbikeparts •</p>
    <p>Holzwindenerstr. 38 • 4221 Steyregg, AT</p>
</body>

</html>
<!-- Stellen Sie wie im Dokument dargestellt alle Rechnungsdaten dar
(Rechnungsnummer, Rechnungsdatum, alle Positionen mit den dargestellten
Informationen, Versand- und Lieferadresse, Zahlungsvariante, Versandart). -->
<?php
$sql = "
SELECT 
    tbl_kunden.*, tbl_staaten.Staat
FROM 
    tbl_kunden
INNER JOIN 
    tbl_staaten
ON 
    tbl_kunden.FIDStaat = tbl_staaten.IDStaat;
";
$resultKunde = $conn->query($sql) or die("Fehler in der Query " . $conn->error . "<br>" . $sql);
while ($row = $resultKunde->fetch_assoc()) {
    echo $row["Vorname"] . " " . $row["Nachname"] . "<br>";
    echo $row["Adresse"] . "<br>";
    echo $row["Ort"] . "<br>";
    echo $row["Staat"] . "<br>";
    echo "per Email: " . $row["Emailadresse"];

    if ($row["IDKunde"] != 0) {
        $sql2 = "
        SELECT 
            tbl_rechnungen.*, tbl_kunden.IDKunde
        FROM 
            tbl_rechnungen
        INNER JOIN 
            tbl_kunden
        ON 
            tbl_rechnungen.FIDKunde = tbl_kunden.IDKunde
        WHERE 
            tbl_kunden.IDKunde = " . $row["IDKunde"] . "
        ;";
        $resultRechnung = $conn->query($sql2) or die("Fehler in der Query " . $conn->error . "<br>" . $sql2);
        while ($row = $resultRechnung->fetch_assoc()) {
            echo "<h3>Rechnung Nr.: " . $row["ReNo"] . "</h3>";
            echo "<h3>Rechnungsdatum: " . $row["Datum"] . "</h3>";

            if ($row["IDRechnung"] != 0) {
                $sql3 = "
                SELECT 
                    tbl_positionen.*, tbl_ustsaetze.*,
                    tbl_produkte.IDProdukt, tbl_produkte.Produkt, tbl_produkte.Beschreibung as 'besh', 
                    tbl_produkte.PreisExkl, tbl_produkte.FIDUStSatz, 
                    tbl_rechnungen.*, tbl_zahlungsarten.*, tbl_versand_paket.*, tbl_versandarten.Versandart
                FROM 
                    tbl_positionen
                INNER JOIN 
                    tbl_produkte
                ON 
                    tbl_positionen.FIDProdukt = tbl_produkte.IDProdukt
                INNER JOIN 
                    tbl_ustsaetze
                ON 
                    tbl_produkte.FIDUStSatz = tbl_ustsaetze.IDUStSatz
                INNER JOIN 
                    tbl_rechnungen
                ON 
                    tbl_positionen.FIDRechnung = tbl_rechnungen.IDRechnung
                INNER JOIN 
                    tbl_zahlungsarten
                ON 
                    tbl_rechnungen.FIDZahlungsart = tbl_zahlungsarten.IDZahlungsart
                INNER JOIN 
                    tbl_versand_paket
                ON 
                    tbl_rechnungen.FIDVersandPaket = tbl_versand_paket.IDVersandPaket
                INNER JOIN 
                    tbl_versandarten
                ON 
                    tbl_versand_paket.FIDVersandart = tbl_versandarten.IDVersandart
                WHERE 
                    tbl_rechnungen.IDRechnung = " . $row["IDRechnung"] . "
                ;";
                $resultPosition = $conn->query($sql3) or die("Fehler in der Query " . $conn->error . "<br>" . $sql3);
                while ($row = $resultPosition->fetch_assoc()) {
                    // echo $row["Anzahl"] . " " . $row["Produkt"] . $row["besh"];
                    // echo "<hr>";

                    $prozent = str_replace("%", "", $row["Beschreibung"]);
                    $ust = $row["PreisExkl"] * $prozent / 100;
                    $preis = $row["PreisExkl"] + $ust;
                    $gesamt = $preis * $row["Anzahl"];
                    $format = number_format($gesamt, 2, ',', '');
                    echo "<table style='width:100%'>";
                    echo "<tr>";
                    echo "     <td>" . $row["Anzahl"] . "</td>";
                    echo "     <td>" . $row["Produkt"] . "<br>" . $row["besh"] . "</td>";
                    echo "     <td>Ihr Preis: " . $format . "<br>inkl " . $ust . "USt. </td>";
                    echo "</tr>";
                    echo "<hr>";
                }
            }
        }
    }
}
?>