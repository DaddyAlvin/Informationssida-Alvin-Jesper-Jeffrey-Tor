<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>visa_blomma</title>
    <link rel="stylesheet" href="index.css">
</head>

<?php
// Anslutning till databasen
$host = "phpmyadmin.ntigskovde.se";
$dbname = "ntigskov_blomma";
$username = "ntigskov_blomuser";
$password = "*Rr-+=_H+NjZ";

$conn = mysqli_connect($host, $username, $password, $dbname);

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

        <!-- Navigationsmeny -->
        <ul id="hamburgerMenu" class="nav-menu">
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

                    echo "<li><a href='#' onclick=\"toggleMenu('slakt_$slaktID', 1)\">$slaktTyp</a>";
                    echo "<ul id='slakt_$slaktID' class='submenu level-1'>";

                    // Hämta färger för varje släktträd
                    $fargQuery = "SELECT DISTINCT f.ID FROM blomma_farg f JOIN blomma_blomma b ON f.ID = b.farg_id WHERE b.slaktrad_id = $slaktID";
                    $fargResult = mysqli_query($conn, $fargQuery);

                    if ($fargResult) {
                        while ($farg = mysqli_fetch_assoc($fargResult)) {
                            $fargID = (int)$farg['ID'];
                            $fargNamn = getTranslation($conn, $lang, "farg_$fargID");

                            echo "<li><a href='#' onclick=\"toggleMenu('farg_$fargID', 2)\">$fargNamn</a>";
                            echo "<ul id='farg_$fargID' class='submenu level-2'>";

                            // Hämta blommor för varje färg
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

        <!-- Dark mode-knapp -->
        <div class="toggle-knappholder" onclick="toggleDarkMode()">
            <div class="toggle-knapp"></div>
        </div>

        <!-- Språkväljare -->
        <form method="get" action="" class="language-switcher">
            <input type="hidden" name="blomma_id" value="<?php echo isset($_GET['blomma_id']) ? intval($_GET['blomma_id']) : ''; ?>">
            <select name="lang" onchange="this.form.submit()">
                <option value="sv" <?php echo ($lang === 'sv') ? 'selected' : ''; ?>>🌸 Svenska</option>
                <option value="en" <?php echo ($lang === 'en') ? 'selected' : ''; ?>>🌼 English</option>
            </select>
        </form>
    </div>
</header>
</nav>

<main>
<?php
// Kontrollera om en blomma är vald
if (isset($_GET['blomma_id'])) {
    $blomma_id = intval($_GET['blomma_id']);

    // Hämta information om den valda blomman
    $sql = "SELECT 
                b.namn, 
                f.farg, 
                s.typ AS slaktrad, 
                CASE WHEN ? = 'en' THEN bdesc.dikt_en ELSE bdesc.dikt END AS dikt,
                CASE WHEN ? = 'en' THEN bdesc.beskrivning_en ELSE bdesc.beskrivning END AS beskrivning,
                CASE WHEN ? = 'en' THEN bdesc.historia_en ELSE bdesc.historia END AS historia,
                bdesc.bild
            FROM blomma_blomma AS b
            LEFT JOIN blomma_farg AS f ON b.farg_id = f.ID
            LEFT JOIN blomma_slaktrad AS s ON b.slaktrad_id = s.ID
            LEFT JOIN blomma_beskrivning AS bdesc ON b.ID = bdesc.blomma_id
            WHERE b.ID = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssi", $lang, $lang, $lang, $blomma_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        echo "<div class='info-box'>";

        // Visa blomman och dess information
        echo "<h2>" . getTranslation($conn, $lang, "blomma_" . $blomma_id) . "</h2>";
        echo "<hr style='border: 1px solid black;'>";

        if (!empty($row['dikt'])) {
            echo "<h3>" . (($lang === 'en') ? 'Poem' : 'Dikt') . "</h3><p>" . nl2br(htmlspecialchars($row['dikt'])) . "</p>";
        }
        if (!empty($row['beskrivning'])) {
            echo "<h3>" . (($lang === 'en') ? 'Description' : 'Beskrivning') . "</h3><p>" . nl2br(htmlspecialchars($row['beskrivning'])) . "</p>";
        }
        if (!empty($row['historia'])) {
            echo "<h3>" . (($lang === 'en') ? 'History' : 'Historia') . "</h3><p>" . nl2br(htmlspecialchars($row['historia'])) . "</p>";
        }
        if (!empty($row['bild'])) {
            echo "<h3>" . (($lang === 'en') ? 'Image' : 'Bild') . "</h3><img src='" . htmlspecialchars($row['bild']) . "' alt='Bild på blomman' style='max-width:300px;'>";
        }
        echo "</div>";
    } else {
        echo "<p>" . (($lang === 'en') ? 'No data found for the selected flower.' : 'Ingen data hittades för vald blomma.') . "</p>";
    }
    mysqli_stmt_close($stmt);
} else {
    echo "<p>" . (($lang === 'en') ? 'No flower selected.' : 'Ingen blomma vald.') . "</p>";
}
mysqli_close($conn);
?>
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
