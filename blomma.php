<!DOCTYPE html>
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
?>
<nav>
    <button onclick="toggleMenu('hamburgerMenu')" style="background-color:blue; color:white; padding:10px; border:none; cursor:pointer;">☰ Menu</button>
    <ul id="hamburgerMenu" style="display:none; list-style-type:none; padding:0; background-color:#f9f9f9; border:1px solid #ccc; position:absolute;">
        <?php
        $result = mysqli_query($conn, $sql);
        if ($result) {
            while ($row = mysqli_fetch_row($result)) {
                echo '<li style="margin:5px 0;">';
                echo '<a href="?table=' . urlencode($row[0]) . '" style="text-decoration:none; color:blue;">' . htmlspecialchars($row[0]) . '</a>';
                echo '</li>';
            }
        } else {
            echo "<li>Error retrieving tables: " . mysqli_error($conn) . "</li>";
        }
        ?>
    </ul>
</nav>

<script>
function toggleMenu(id) {
    var element = document.getElementById(id);
    if (element.style.display === "none" || element.style.display === "") {
        element.style.display = "block";
    } else {
        element.style.display = "none";
    }
}
</script>
