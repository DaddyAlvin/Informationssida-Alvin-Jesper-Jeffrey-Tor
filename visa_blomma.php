<link rel="stylesheet" href="index.css">

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = "phpmyadmin.ntigskovde.se";
$dbname = "ntigskov_blomma";
$username = "ntigskov_blomuser";
$password = "*Rr-+=_H+NjZ";

$conn = mysqli_connect($host, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_GET['blomma_id'])) {
    $blomma_id = intval($_GET['blomma_id']);

    $sql = "SELECT b.namn, f.farg, s.typ AS slaktrad, d.dikt, bi.bild, bdesc.beskrivning, h.historia
    FROM blomma_blomma AS b
    LEFT JOIN blomma_farg AS f ON b.farg_id = f.ID
    LEFT JOIN blomma_slaktrad AS s ON b.slaktrad_id = s.ID
    LEFT JOIN blomma_dikt AS d ON b.ID = d.blomma_id
    LEFT JOIN blomma_bild AS bi ON b.ID = bi.blomma_id
    LEFT JOIN blomma_beskrivning AS bdesc ON b.ID = bdesc.blomma_id
    LEFT JOIN blomma_historia AS h ON b.ID = h.blomma_id
    WHERE b.ID = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $blomma_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        echo "<div class='info-box'>";
        echo "<h1>" . htmlspecialchars($row['namn']) . "</h1>";
        echo "<p>Färg: " . htmlspecialchars($row['farg']) . "</p>";
        echo "<p>Släkträd: " . htmlspecialchars($row['slaktrad']) . "</p>";
        
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
        echo "<p>Ingen data hittades för vald blomma.</p>";
    }

    mysqli_stmt_close($stmt);
} else {
    echo "<p>Ingen blomma vald.</p>";
}

mysqli_close($conn);
?>
<br>
<button> <a href="index.php">Tillbaka till grundfilen</a> </button>
