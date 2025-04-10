<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Visa blomma</title>
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

<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f9;
    margin: 0;
    padding: 0;
}

a {
    text-decoration: none;
}

nav {
    background-color: #333;
    color: white;
    padding: 10px;
}

button {
    background-color: #333;
    color: white;
    padding: 10px;
    border: none;
    cursor: pointer;
    font-size: 18px;
    width: 100%;
}

button:hover {
    background-color: #444;
}

ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
}

li {
    margin: 5px 0;
}

.submenu {
    display: none;
    margin-left: 20px;
    background-color: #444;
    border-radius: 4px;
}

.submenu a {
    color: white;
    padding: 5px;
    display: block;
}

.submenu a:hover {
    background-color: #555;
}

.submenu.level-0 {
    background-color: #333;
}

body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    background-image: url('bilder/blommonsterbkg.png');
    background-size: cover;
    background-repeat: no-repeat;
    background-attachment: fixed;
    color: black;
}

.top-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 25px 40px;
    background-color: #EFE4B0;
    border-bottom: 4px solid #FFC90E;
}

.menu-icon {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    width: 55px;
    height: 45px;
    cursor: pointer;
    padding: 12px;
}

.menu-icon div {
    background-color: black;
    height: 8px;
    width: 100%;
}

.top-nav {
    display: flex;
    gap: 50px;
}

.top-nav a {
    text-decoration: none;
    font-size: 24px;
    font-weight: bold;
    color: black;
    padding: 14px 30px;
    background-color: #d7c675;
    border: 4px solid #FFC90E;
    border-radius: 10px;
    transition: background-color 0.3s;
}

.top-nav a:hover {
    background-color: #aea050;
}

.nav-menu {
    display: none;
    position: absolute;
    top: 90px;
    left: 20px;
    background: #EFE4B0;
    width: 220px;
    border: 4px solid #FFC90E;
    z-index: 1000;
}

.nav-menu a {
    display: block;
    padding: 15px;
    text-decoration: none;
    color: black;
    border-bottom: 4px solid #FFC90E;
}

.nav-menu a:hover {
    background-color: #FFC90E;
}

.nav-menu.show {
    display: block;
}

main {
    padding: 40px 30px;
}

.row {
    display: flex;
    flex-wrap: wrap;
    gap: 40px;
    justify-content: center;
    margin-bottom: 40px;
}

.poem {
    display: flex;
    justify-content: center;
    margin-bottom: 40px;
}

.flex-box {
    background-color: #EFE4B0;
    border: 4px solid #FFC90E;
    padding: 15px;
    flex: 1 1 300px;
    box-sizing: border-box;
    color: black;
    display: flex;
    align-items: center;
}

.flex-box p {
    margin: 0;
    white-space: pre-line;
    word-wrap: break-word;
    font-size: 16px;
}

.wide {
    width: 90%;
}
    </style>