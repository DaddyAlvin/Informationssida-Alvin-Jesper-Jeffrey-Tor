<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Visa blomma</title>
    <link rel="stylesheet" href="visa_blomma.css">
</head>
<body>
<?php
$host = "phpmyadmin.ntigskovde.se";
$dbname = "ntigskov_blomma";
$username = "ntigskov_blomuser";
$password = "*Rr-+=_H+NjZ";

$conn = mysqli_connect($host, $username, $password, $dbname);

if (mysqli_connect_errno()) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_GET['blomma_id'])) {
    $blomma_id = intval($_GET['blomma_id']);

    $sql = "
    SELECT 
        blomma.blomma,
        farg.farg,
        slaktrad.typ AS slaktrad,
        GROUP_CONCAT(DISTINCT blomma_sprak.oversatt_blomma SEPARATOR ', ') AS oversattningar
    FROM blomma
    LEFT JOIN blomma_farg ON blomma.ID = blomma_farg.blomma_id
    LEFT JOIN farg ON blomma_farg.farg_id = farg.ID
    LEFT JOIN blomma_slaktrad ON blomma.ID = blomma_slaktrad.blomma_id
    LEFT JOIN slaktrad ON blomma_slaktrad.slaktrad_id = slaktrad.ID
    LEFT JOIN blomma_sprak ON blomma.ID = blomma_sprak.blomma_id
    WHERE blomma.ID = $blomma_id
    GROUP BY blomma.ID
    ";

    $result = mysqli_query($conn, $sql);

    if ($row = mysqli_fetch_assoc($result)) {
        echo "<h1>" . htmlspecialchars($row['blomma']) . "</h1>";
        echo "<p><strong>Färg:</strong> " . htmlspecialchars($row['farg']) . "</p>";
        echo "<p><strong>Släktträd:</strong> " . htmlspecialchars($row['slaktrad']) . "</p>";
        echo "<p><strong>Översättningar:</strong> " . htmlspecialchars($row['oversattningar']) . "</p>";
    } else {
        echo "<p>Ingen data hittades.</p>";
    }
} else {
    echo "<p>Ingen blomma vald.</p>";
}
?>

<a href="blomma.php" style="display:block; padding:10px; color:#333;">Tillbaka till grundfilen</a>

</body>
</html>
