<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AboutUs</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
<?php
// Anslut till databasen
$host = "phpmyadmin.ntigskovde.se";
$dbname = "ntigskov_blomma";
$username = "ntigskov_blomuser";
$password = "*Rr-+=_H+NjZ";

$conn = mysqli_connect($host, $username, $password, $dbname);
if (mysqli_connect_errno()) {
    die("Connection failed: " . mysqli_connect_error());
}

// Kontrollera vilket språk som används, standard är svenska
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'sv';

// Funktion för att hämta översättningar från databasen
function getTranslation($conn, $lang, $key_name) {
    $stmt = $conn->prepare("SELECT content FROM blomma_språk WHERE lang = ? AND key_name = ?");
    $stmt->bind_param("ss", $lang, $key_name);
    $stmt->execute();
    $stmt->bind_result($content);
    if ($stmt->fetch()) {
        return $content;
    } else {
        return "[$key_name saknas i $lang]";
    }
    $stmt->close();
}

// Hämta översättningar för olika texter
$aboutUsTitle = getTranslation($conn, $lang, "about_us_title");
$aboutUsDescription1 = getTranslation($conn, $lang, "about_us_description_1");
$aboutUsDescription2 = getTranslation($conn, $lang, "about_us_description_2");
$buttonClose = getTranslation($conn, $lang, "buttons.close");
$buttonReddit = getTranslation($conn, $lang, "buttons.reddit");
$buttonInstagram = getTranslation($conn, $lang, "buttons.instagram");
?>
<body>
<nav>
<header>
    <div class="top-bar">
        <!-- Menyikon för hamburgarmenyn -->
        <div class="menu-icon" onclick="toggleMenu('hamburgerMenu', 0)">
            <div></div>
            <div></div>
            <div></div>
        </div>
        <ul id="hamburgerMenu" class="nav-menu">
            <!-- Länkar till startsidan och Om oss-sidan -->
            <a href="index.php?lang=<?php echo $lang; ?>"><strong><?php echo ($lang === 'en') ? 'Home' : 'Hem'; ?></strong></a>
            <a href="AboutUs.php?lang=<?php echo $lang; ?>"><strong><?php echo ($lang === 'en') ? 'About Us' : 'Om oss'; ?></strong></a>
            <br>
            <?php
            // Hämta alla släktträd från databasen
            $slaktradQuery = "SELECT ID FROM blomma_slaktrad";
            $slaktradResult = mysqli_query($conn, $slaktradQuery);

            if ($slaktradResult) {
                while ($slakt = mysqli_fetch_assoc($slaktradResult)) {
                    $slaktID = (int)$slakt['ID'];
                    $slaktTyp = getTranslation($conn, $lang, "slaktrad_$slaktID");

                    // Visa släktträd i menyn
                    echo "<li><a href='#' onclick=\"toggleMenu('slakt_$slaktID', 1)\">$slaktTyp</a>";
                    echo "<ul id='slakt_$slaktID' class='submenu level-1'>";

                    // Hämta färger för varje släktträd
                    $fargQuery = "SELECT DISTINCT f.ID FROM blomma_farg f JOIN blomma_blomma b ON f.ID = b.farg_id WHERE b.slaktrad_id = $slaktID";
                    $fargResult = mysqli_query($conn, $fargQuery);

                    if ($fargResult) {
                        while ($farg = mysqli_fetch_assoc($fargResult)) {
                            $fargID = (int)$farg['ID'];
                            $fargNamn = getTranslation($conn, $lang, "farg_$fargID");

                            // Visa färger i undermenyn
                            echo "<li><a href='#' onclick=\"toggleMenu('farg_$fargID', 2)\">$fargNamn</a>";
                            echo "<ul id='farg_$fargID' class='submenu level-2'>";

                            // Hämta blommor för varje färg
                            $blommaQuery = "SELECT b.ID FROM blomma_blomma b WHERE b.farg_id = $fargID AND b.slaktrad_id = $slaktID";
                            $blommaResult = mysqli_query($conn, $blommaQuery);

                            if ($blommaResult) {
                                while ($blomma = mysqli_fetch_assoc($blommaResult)) {
                                    $blommaID = (int)$blomma['ID'];
                                    $blommaNamn = getTranslation($conn, $lang, "blomma_$blommaID");

                                    // Visa blommor som länkar
                                    echo "<li><a href='visa_blomma.php?blomma_id=$blommaID&lang=$lang'>$blommaNamn</a></li>";
                                }
                            } else {
                                echo "<li>Inga blommor hittades för denna färg.</li>";
                            }

                            echo "</ul></li>";
                        }
                    } else {
                        echo "<li>Inga färger hittades för detta släktträd.</li>";
                    }

                    echo "</ul></li>";
                }
            } else {
                echo "<li>Inga släktträd hittades.</li>";
            }
            ?>
        </ul>
        <div class="welcome-message">
            <div class="vårlåda">
                <!-- Visa välkomstmeddelande och knappar -->
                <h1><?php echo $aboutUsTitle; ?></h1>
                <p><?php echo $aboutUsDescription1; ?></p>
                <p><?php echo $aboutUsDescription2; ?></p>
                <button><?php echo $buttonClose; ?></button>
                <button><?php echo $buttonReddit; ?></button>
                <button><?php echo $buttonInstagram; ?></button>
            </div>
        </div>
        <!-- Dark mode-knapp -->
        <div class="toggle-knappholder" onclick="toggleDarkMode()">
            <div class="toggle-knapp"></div>
        </div>
        <!-- Språkväxlare -->
        <form method="get" action="" class="language-switcher">
                <select name="lang" onchange="this.form.submit()">
                    <option value="sv" <?php echo ($lang === 'sv') ? 'selected' : ''; ?>>🌸 Svenska</option>
                    <option value="en" <?php echo ($lang === 'en') ? 'selected' : ''; ?>>🌼 English</option>
                </select>
            </form>
    </div>
</header>
</nav>
<div class="image-container">
    <!-- Bild på sidan -->
    <img src="bilder/vi.png" alt="" style="width:25%; height:auto;">
</div>
<script>
// Funktion för att visa/dölja menyer
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

// Funktion för att växla mörkt läge
function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
    const enabled = document.body.classList.contains('dark-mode');
    localStorage.setItem('dark-mode', enabled ? 'enabled' : 'disabled');
}

// Kontrollera om mörkt läge är aktiverat vid sidladdning
if (localStorage.getItem('dark-mode') === 'enabled') {
    document.body.classList.add('dark-mode');
}
</script>
