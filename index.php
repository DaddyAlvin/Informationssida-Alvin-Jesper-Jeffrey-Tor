<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>blomma_info</title>
    <link rel="stylesheet" href="index.css">
</head>

<?php
// Databasanslutning
$host = "phpmyadmin.ntigskovde.se";
$dbname = "ntigskov_blomma";
$username = "ntigskov_blomuser";
$password = "*Rr-+=_H+NjZ";

$conn = mysqli_connect($host, $username, $password, $dbname);

// Kontrollera språk, standard är svenska
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
        return "[$key_name missing in $lang]";
    }
    $stmt->close();
}

// Hämta översättningar för olika texter
$intro1 = getTranslation($conn, $lang, "intro1");
$intro2 = getTranslation($conn, $lang, "intro2");
$poem = getTranslation($conn, $lang, "poem");
$fascination = getTranslation($conn, $lang, "fascination");
?>

<body>
<nav>
<header>
    <div class="top-bar">
        <!-- Hamburgermeny -->
        <div class="menu-icon" onclick="toggleMenu('hamburgerMenu', 0)">
            <div></div>
            <div></div>
            <div></div>
        </div>

        <!-- Navigationsmeny -->
        <ul id="hamburgerMenu" class="nav-menu">
            <a href="index.php?lang=<?php echo $lang; ?>"><strong><?php echo ($lang === 'en') ? 'Home' : 'Hem'; ?></strong></a>
            <a href="AboutUs.php?lang=<?php echo $lang; ?>"><strong><?php echo ($lang === 'en') ? 'About Us' : 'Om oss'; ?></strong></a>
            <br>
            
            <?php
            // Hämta släktträd från databasen
            $slaktradQuery = "SELECT ID FROM blomma_slaktrad";
            $slaktradResult = mysqli_query($conn, $slaktradQuery);

            if ($slaktradResult) {
                while ($slakt = mysqli_fetch_assoc($slaktradResult)) {
                    $slaktID = (int)$slakt['ID'];
                    $slaktTyp = getTranslation($conn, $lang, "slaktrad_$slaktID"); 

                    echo "<li><a href='#' onclick=\"toggleMenu('slakt_$slaktID', 1)\">$slaktTyp</a>";
                    echo "<ul id='slakt_$slaktID' class='submenu level-1'>";

                    // Hämta färger för släktträdet
                    $fargQuery = "SELECT DISTINCT f.ID FROM blomma_farg f JOIN blomma_blomma b ON f.ID = b.farg_id WHERE b.slaktrad_id = $slaktID";
                    $fargResult = mysqli_query($conn, $fargQuery);

                    if ($fargResult) {
                        while ($farg = mysqli_fetch_assoc($fargResult)) {
                            $fargID = (int)$farg['ID'];
                            $fargNamn = getTranslation($conn, $lang, "farg_$fargID");

                            echo "<li><a href='#' onclick=\"toggleMenu('farg_$fargID', 2)\">$fargNamn</a>";
                            echo "<ul id='farg_$fargID' class='submenu level-2'>";

                            // Hämta blommor för färgen
                            $blommaQuery = "SELECT b.ID FROM blomma_blomma b WHERE b.farg_id = $fargID AND b.slaktrad_id = $slaktID";
                            $blommaResult = mysqli_query($conn, $blommaQuery);

                            if ($blommaResult) {
                                while ($blomma = mysqli_fetch_assoc($blommaResult)) {
                                    $blommaID = (int)$blomma['ID'];
                                    $blommaNamn = getTranslation($conn, $lang, "blomma_$blommaID"); 

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
        
        <!-- Välkomstmeddelande -->
        <div class="welcome-message">
            <h2>
                <?php 
                echo ($lang === 'en') 
                    ? "Welcome to our flower page!" 
                    : "Välkommen till vår sida om blommor!";
                ?>
            </h2>
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

<main>
    <!-- Introduktionstexter -->
    <section class="row">
        <div class="flex-box">
            <p><?php echo $intro1; ?></p>
        </div>
        <div class="flex-box">
            <p><?php echo $intro2; ?></p>
        </div>
    </section>

    <!-- Diktsektion -->
    <section class="poem">
        <div class="flex-box wide">
            <p><?php echo $poem; ?></p>
        </div>
    </section>

    <!-- Fascinationssektion -->
    <section class="row">
        <div class="flex-box" id="fascination-text">
            <p><?php echo $fascination; ?></p>
        </div>
        <div class="flex-box">
            <img src="bilder/hemsidablommor.jpg" alt="Flowers" />
        </div>
    </section>
</main>

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

// Kontrollera om mörkt läge är aktiverat
if (localStorage.getItem('dark-mode') === 'enabled') {
    document.body.classList.add('dark-mode');
}
</script>
</body>
</html>
