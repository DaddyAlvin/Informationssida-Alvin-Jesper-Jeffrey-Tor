<?php
$host = "phpmyadmin.ntigskovde.se";
$dbname = "ntigskov_blomma";
$username = "ntigskov_blomuser";
$password = "*Rr-+=_H+NjZ";

$conn = mysqli_connect($host, $username, $password, $dbname);
if (mysqli_connect_errno()) {
    die("Connection failed: " . mysqli_connect_error());
}
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blommor - Meny</title>


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
</head>
<body>

<a href="blomma.php" style="display:block; padding:10px; color:#333;">Tillbaka till grundfilen</a>
<a href="AboutUs.php" style="display:block; padding:10px; color:#333;">Till om oss sida</a>

<nav>
    <button onclick="toggleMenu('hamburgerMenu', 0)">☰ Menu</button>
    <ul id="hamburgerMenu" class="submenu level-0">
        <?php
        $slaktradQuery = "SELECT * FROM slaktrad";
        $slaktradResult = mysqli_query($conn, $slaktradQuery);

        while ($slakt = mysqli_fetch_assoc($slaktradResult)) {
            $slaktID = $slakt['ID'];
            $slaktTyp = htmlspecialchars($slakt['typ']);
            echo "<li><a href='#' onclick=\"toggleMenu('slakt_$slaktID', 1)\">$slaktTyp</a>";
            echo "<ul id='slakt_$slaktID' class='submenu level-1'>";

            $fargQuery = "SELECT DISTINCT farg.ID, farg.farg FROM farg
                          JOIN blomma_farg ON farg.ID = blomma_farg.farg_id
                          JOIN blomma ON blomma.ID = blomma_farg.blomma_id
                          JOIN blomma_slaktrad ON blomma.ID = blomma_slaktrad.blomma_id
                          WHERE blomma_slaktrad.slaktrad_id = $slaktID";
            $fargResult = mysqli_query($conn, $fargQuery);

            while ($farg = mysqli_fetch_assoc($fargResult)) {
                $fargID = $farg['ID'];
                $fargNamn = htmlspecialchars($farg['farg']);
                echo "<li><a href='#' onclick=\"toggleMenu('farg_{$slaktID}_{$fargID}', 2)\">$fargNamn</a>";
                echo "<ul id='farg_{$slaktID}_{$fargID}' class='submenu level-2'>";

                $blommaQuery = "SELECT blomma.ID, blomma.blomma FROM blomma
                                JOIN blomma_farg ON blomma.ID = blomma_farg.blomma_id
                                JOIN blomma_slaktrad ON blomma.ID = blomma_slaktrad.blomma_id
                                WHERE blomma_farg.farg_id = $fargID AND blomma_slaktrad.slaktrad_id = $slaktID";
                $blommaResult = mysqli_query($conn, $blommaQuery);

                while ($blomma = mysqli_fetch_assoc($blommaResult)) {
                    $blommaID = $blomma['ID'];
                    $blommaNamn = htmlspecialchars($blomma['blomma']);
                    echo "<li><a href='visa_blomma.php?blomma_id=$blommaID'>$blommaNamn</a></li>";
                }

                echo "</ul></li>";
            }

            echo "</ul></li>";
        }
        ?>
    </ul>
</nav>


<script>
function toggleMenu(id, level) {
    const all = document.querySelectorAll('.submenu.level-' + level);
    all.forEach(el => {
        if (el.id !== id) el.style.display = "none";
    });

    const current = document.getElementById(id);
    if (current) {
        current.style.display = (current.style.display === "block") ? "none" : "block";
    }
}
</script>

</body>
</html>
