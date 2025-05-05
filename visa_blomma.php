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

// Språkval
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'sv';

// Språktexter
if ($lang === 'en') {
    $intro1 = "Here you can explore various flowers, their colors, and relationships. Learn more about their meanings and uses throughout history.";
    $intro2 = "Flowers are nature's masterpieces, filled with colors and fragrances that brighten our world. Explore their beauty and discover their unique characteristics.";
    $poem   = "Flowers dancing in the wind's embrace, spreading their beauty across land and space. With colors that glow, scents that cheer, nature's gift that we hold dear.";
    $fascination = "Flowers have fascinated people for millennia and are a symbol of beauty, love, and the transience of life. They play an important role in the ecosystem by attracting pollinators such as bees, butterflies, and birds. Flowers are also used in art, literature, and culture to express emotions and tell stories. From roses and lilies to exotic orchids, each flower has its own unique meaning and charm. They are not only decorative but also have medicinal and culinary uses, making them an invaluable part of our world.";
} else {
    $intro1 = "Här kan du utforska olika blommor, deras färger och relationer. Lär dig mer om deras betydelser och användning genom historien.";
    $intro2 = "Blommor är naturens konstverk, fyllda med färger och dofter som förgyller vår värld. Utforska deras skönhet och upptäck deras unika egenskaper.";
    $poem   = "Blommor dansar i vindens famn, sprider sin skönhet i land och namn. Med färger som glöder, dofter som ler, naturens gåva vi håller så kär.";
    $fascination = "Blommor har fascinerat människor i årtusenden och är en symbol för skönhet, kärlek och livets förgänglighet. De spelar en viktig roll i ekosystemet genom att locka pollinerare som bin, fjärilar och fåglar. Blommor används också i konst, litteratur och kultur för att uttrycka känslor och berätta historier. Från rosor och liljor till exotiska orkidéer, varje blomma bär på en unik betydelse och charm. De är inte bara dekorativa utan har även medicinska och kulinariska användningsområden, vilket gör dem till en ovärderlig del av vår värld.";
}
?>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="index.css" />
    <title>Blommor</title>
</head>

<body>
<header>
    <div class="top-bar">
        <!-- Menyikon -->
        <div class="menu-icon" onclick="toggleMenu('hamburgerMenu', 0)">
            <div></div>
            <div></div>
            <div></div>
        </div>

        <!-- Navigationsmeny -->
        <ul id="hamburgerMenu" class="nav-menu">
            <a href="index.php"><strong>Hem</strong></a>
            <a href="AboutUs.html"><strong>Om oss</strong></a>
            <br>

            <?php
            // Hämta släktträd
            $slaktradQuery = "SELECT ID, typ FROM blomma_slaktrad";
            $slaktradResult = mysqli_query($conn, $slaktradQuery);

            if ($slaktradResult) {
                while ($slakt = mysqli_fetch_assoc($slaktradResult)) {
                    $slaktID = (int)$slakt['ID'];
                    $slaktTyp = htmlspecialchars($slakt['typ']);

                    echo "<li><a href='#' onclick=\"toggleMenu('slakt_$slaktID', 1)\">$slaktTyp</a>";
                    echo "<ul id='slakt_$slaktID' class='submenu level-1'>";

                    // Färger för detta släktträd
                    $fargQuery = "SELECT DISTINCT f.ID, f.farg FROM blomma_farg f JOIN blomma_blomma b ON f.ID = b.farg_id WHERE b.slaktrad_id = $slaktID";
                    $fargResult = mysqli_query($conn, $fargQuery);

            if ($fargResult) {
                while ($farg = mysqli_fetch_assoc($fargResult)) {
                            $fargID = (int)$farg['ID'];
                            $fargNamn = htmlspecialchars($farg['farg']);

                            echo "<li><a href='#' onclick=\"toggleMenu('farg_$fargID', 2)\">$fargNamn</a>";
                            echo "<ul id='farg_$fargID' class='submenu level-2'>";

                            // Blommor för färg och släktträd
                            $blommaQuery = "SELECT b.ID, b.namn FROM blomma_blomma b WHERE b.farg_id = $fargID AND b.slaktrad_id = $slaktID";
                            $blommaResult = mysqli_query($conn, $blommaQuery);

            if ($blommaResult) {
                while ($blomma = mysqli_fetch_assoc($blommaResult)) {
                        $blommaID = (int)$blomma['ID'];
                        $blommaNamn = htmlspecialchars($blomma['namn']);

                        echo "<li><a href='visa_blomma.php?blomma_id=$blommaID'>$blommaNamn</a></li>";
                    }} 
            else {
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
        <h2>
             <?php 
            echo ($lang === 'en') 
                ? "Welcome to our flower page!" 
                : "Välkommen till vår sida om blommor!";
            ?>
        </h2>
            </div>

        <!-- Språkval -->
        <form method="get" action="" class="language-switcher">
            <select name="lang" onchange="this.form.submit()">
            <option value="sv" <?php echo ($lang === 'sv') ? 'selected' : ''; ?>>🌸 Svenska</option>
            <option value="en" <?php echo ($lang === 'en') ? 'selected' : ''; ?>>🌼 English</option>
            </select>
        </form>
            </select>
        </form>
    </div>
</header>

<main>
    <section class="row">
        <div class="flex-box">
            <p><?php echo $intro1; ?></p>
        </div>
        <div class="flex-box">
            <p><?php echo $intro2; ?></p>
        </div>
    </section>

    <section class="poem">
        <div class="flex-box wide">
            <p><?php echo $poem; ?></p>
        </div>
    </section>

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
