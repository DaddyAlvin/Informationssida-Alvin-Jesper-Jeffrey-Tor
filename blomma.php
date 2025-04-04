<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>blomma</title>
</head>
<body>
    <h1>Välkommen till blomman.php sidan</h1>
    <p>Dagens datum är: <?php echo date('Y-m-d'); ?></p>
</body>
</html>

<?php
$host = "phpmyadmin.ntigskovde.se";
$dbname = "ntigskov_blomma";
$username = "ntigskov_blomuser";
$password = "*Rr-+=_H+NjZ";

$conn = mysqli_connect($host, $username, $password, $dbname);

if (mysqli_connect_errno()) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "SHOW TABLES";

echo '<nav>';
echo '<ul style="list-style-type:none; padding:0;">';

$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_row($result)) {
        echo '<li style="margin:5px 0;">';
        echo '<a href="#" onclick="toggleMenu(\'' . htmlspecialchars($row[0]) . '\')" style="text-decoration:none; color:blue;">' . htmlspecialchars($row[0]) . '</a>';
        echo '<ul id="' . htmlspecialchars($row[0]) . '" style="display:none; list-style-type:none; padding-left:20px;">';
        echo '<li><a href="?table=' . urlencode($row[0]) . '" style="text-decoration:none; color:blue;">View Table</a></li>';
        echo '</ul>';
        echo '</li>';
    }
} else {
    echo "Error retrieving tables: " . mysqli_error($conn);
}

echo '</ul>';
echo '</nav>';

$result = mysqli_query($conn, $sql);

if ($result) {
    echo "<h2>Database Tables:</h2><ul>";
    while ($row = mysqli_fetch_row($result)) {
        echo "<li>" . htmlspecialchars($row[0]) . "</li>";
    }
    echo "</ul>";
} else {
    echo "Error retrieving tables: " . mysqli_error($conn);
}

mysqli_close($conn);

?>