<?php
$host = "phpmyadmin.ntigskovde.se";
$dbname = "ntigskov_blomma";
$username = "ntigskov_blomuser";
$password = "*Rr-+=_H+NjZ";

$conn = mysqli_connect($host, $username, $password, $dbname);
?>

<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
</head>
<body>
<header>
    <div class="top-bar">
        <div class="menu-icon" onclick="toggleMenu('hamburgerMenu', 0)">
            <div></div>
            <div></div>
            <div></div>
        </div>
        <ul id="hamburgerMenu" class="nav-menu">
            <?php
            $slaktradQuery = "SELECT * FROM slaktrad";
            $slaktradResult = mysqli_query($conn, $slaktradQuery);
            while ($slakt = mysqli_fetch_assoc($slaktradResult)) {
                $slaktID = $slakt['ID'];
                $slaktTyp = htmlspecialchars($slakt['typ']);
                echo "<li><a href='#' onclick=\"toggleMenu('slakt_$slaktID', 1)\">$slaktTyp</a>";
                echo "<ul id='slakt_$slaktID' class='submenu level-1'>";

                $fargQuery = "SELECT farg.ID, farg.farg FROM farg
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
            if (mysqli_connect_errno()) {
                die("Connection failed: " . mysqli_connect_error());
            }
            ?>
        </ul>

        <nav class="top-nav">
        <div class="language-switcher">
    <form method="get" action="">
        <select name="lang" onchange="this.form.submit()">
            <option value="sv" <?php echo (isset($_GET['lang']) && $_GET['lang'] === 'sv') ? 'selected' : ''; ?>>Svenska</option>
            <option value="en" <?php echo (isset($_GET['lang']) && $_GET['lang'] === 'en') ? 'selected' : ''; ?>>English</option>
        </select>
    </form>
    </div>

        <h2>Välkommen till vår sida om blommor!</h2>
            <a href="index.php">Hem</a>
            <a href="AboutUs.html">Om oss</a>
        </nav>
    </div>
</header>

<main>
    <section class="row">
        <div class="flex-box">
            <p>Här kan du utforska olika blommor, deras färger och släktskap. Lär dig mer om deras betydelser och användningar genom tiderna.</p>
        </div>
        <div class="flex-box">
            <p>För att navigera sidan, använd menyn högst upp till vänster. Klicka på en släkt för att se dess färger, och välj en färg för att utforska blommor i den kategorin. Klicka på en blomma för att få mer information om den.</p>
        </div>
    </section>

    <section class="poem">
        <div class="flex-box wide">
            <p>Blommor som dansar i vindens famn,  sprider sin skönhet över land och hamn.  Med färger som glöder, dofter som ler,  naturens gåva som aldrig förser.</p>
        </div>
    </section>

    <section class="row">
        <div class="flex-box">
            <p>Blommor har fascinerat människor i årtusenden och är en symbol för skönhet, kärlek och livets förgänglighet. De spelar en viktig roll i ekosystemet genom att locka pollinatörer som bin, fjärilar och fåglar. Blommor används också i konst, litteratur och kultur för att uttrycka känslor och berätta historier. Från rosor och liljor till exotiska orkidéer, varje blomma har sin egen unika betydelse och charm. De är inte bara dekorativa utan har också medicinska och kulinariska användningar, vilket gör dem till en ovärderlig del av vår värld.</p>
        </div>
        <div class="flex-box">
            <img src="bilder/hemsidablommor.jpg" alt="Bild på en ros">
        </div>
    </section>
</main>
</body>
</html>

</head>
<body>

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

<?php
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'sv';

if ($lang === 'en') {
    echo "<script>
        document.querySelector('h2').textContent = 'Welcome to our flower page!';
        document.querySelector('.row .flex-box:nth-child(1) p').textContent = 'Here you can explore various flowers, their colors, and relationships. Learn more about their meanings and uses throughout history.';
        document.querySelector('.row .flex-box:nth-child(2) p').textContent = 'To navigate the site, use the menu at the top left. Click on a family to see its colors, and select a color to explore flowers in that category. Click on a flower to get more information about it.';
        document.querySelector('.poem .flex-box p').textContent = 'Flowers dancing in the wind\'s embrace, spreading their beauty across land and space. With colors that glow, scents that cheer, nature\'s gift that we hold dear.';
        document.querySelector('.row .flex-box:nth-child(3) p').textContent = 'Flowers have fascinated people for millennia and are a symbol of beauty, love, and the transience of life. They play an important role in the ecosystem by attracting pollinators such as bees, butterflies, and birds. Flowers are also used in art, literature, and culture to express emotions and tell stories. From roses and lilies to exotic orchids, each flower has its own unique meaning and charm. They are not only decorative but also have medicinal and culinary uses, making them an invaluable part of our world.';
    </script>";
}
?>
