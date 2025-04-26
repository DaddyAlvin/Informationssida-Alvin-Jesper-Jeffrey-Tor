<html>
<head>
    <meta charset="UTF-8">
    <title>Visa blomma</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
<?php
$host = "phpmyadmin.ntigskovde.se";
$dbname = "ntigskov_blomma";
$username = "ntigskov_blomuser";
$password = "*Rr-+=_H+NjZ";

$conn = mysqli_connect($host, $username, $password, $dbname);

if (isset($_GET['blomma_id'])) {
    $blomma_id = intval($_GET['blomma_id']);

    $sql = "
    SELECT 
        blomma.blomma,
        farg.farg,
        slaktrad.typ AS slaktrad,
        GROUP_CONCAT(DISTINCT blomma_sprak.oversatt_blomma SEPARATOR ', ') AS oversattningar,
        dikt.dikt,
        bild.bild,
        beskrivning.beskrivning,
        historia.historia
    FROM blomma
    LEFT JOIN blomma_farg ON blomma.ID = blomma_farg.blomma_id
    LEFT JOIN farg ON blomma_farg.farg_id = farg.ID
    LEFT JOIN blomma_slaktrad ON blomma.ID = blomma_slaktrad.blomma_id
    LEFT JOIN slaktrad ON blomma_slaktrad.slaktrad_id = slaktrad.ID
    LEFT JOIN blomma_sprak ON blomma.ID = blomma_sprak.blomma_id
    LEFT JOIN dikt ON blomma.ID = dikt.blomma_id
    LEFT JOIN bild ON blomma.ID = bild.blomma_id
    LEFT JOIN beskrivning ON blomma.ID = beskrivning.blomma_id
    LEFT JOIN historia ON blomma.ID = historia.blomma_id
    WHERE blomma.ID = $blomma_id
";

$result = mysqli_query($conn, $sql);

    if ($row = mysqli_fetch_assoc($result)) {
        echo "<div class='info-box'>";
        echo "<h1>" . htmlspecialchars($row['blomma']) . "</h1>";
        echo "<p>Färg: " . htmlspecialchars($row['farg']) . "</p>";
        echo "<p>Släktträd: " . htmlspecialchars($row['slaktrad']) . "</p>";
        echo "<p>Översättningar: " . htmlspecialchars($row['oversattningar']) . "</p>";

        if (!empty($row['dikt'])) {
            echo "<h3>Dikt</h3><p>" . nl2br(htmlspecialchars($row['dikt'])) . "</p>";
        }
        if (!empty($row['beskrivning'])) {
            echo "<h3>Beskrivning</h3><p>" . nl2br(htmlspecialchars($row['beskrivning'])) . "</p>";
        }
        if (!empty($row['historia'])) {
            echo "<h3>Historia</h3><p>" . nl2br(htmlspecialchars($row['historia'])) . "</p>";
        }
        if (!empty($row['bild'])) {
            echo "<h3>Bild</h3><img src='" . htmlspecialchars($row['bild']) . "' alt='Bild på blomman' style='max-width:300px;'>";
        }
        echo "</div>";
    } else {
        echo "<p>Ingen data hittades.</p>";
    }
} else {
    echo "<p>Ingen blomma vald.</p>";
}
if (mysqli_connect_errno()) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
<br>
<button> <a href="index.php">Tillbaka till grundfilen</a> </button>
</body>
</html>

