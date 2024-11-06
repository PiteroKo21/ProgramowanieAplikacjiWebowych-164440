<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

/* Sprawdzenie, jaka strona powinna być załadowana */
if ($_GET['idp'] == '') {
    $strona = 'glowna.html';
} elseif ($_GET['idp'] == 'pionierzy') {
    $strona = 'pionierzy.html';
} elseif ($_GET['idp'] == 'technologie') {
    $strona = 'technologie.html';
} elseif ($_GET['idp'] == 'misje') {
    $strona = 'misje.html';
}elseif($_GET['idp'] == 'kontakt'){
    $strona = 'kontakt.html';
}elseif($_GET['idp'] == 'kolorujtlo'){
    $strona = 'kolorujtlo.html';
}elseif($_GET['idp'] == 'timedate'){
    $strona = 'timedate.html';
} elseif($_GET['idp'] == 'lab3'){
    $strona = 'lab3.html';
}elseif($_GET['idp'] == 'filmy'){
    $strona = 'filmy.html';  
}
else {
    $strona = 'html/404.html'; // Strona błędu
}

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../js/kolorujtlo.js"></script>
    <script src="../js/scripts.js"></script>
    <script src="../js/timedate.js"></script>
    <title>Strona Główna</title>
</head>
<body>
    <nav class="navbar">
        <ul>
            <li><a href="index.php">Strona Główna</a></li>
            <li><a href="index.php?idp=pionierzy">Pionierzy Kosmosu</a></li>
            <li><a href="index.php?idp=technologie">Technologie Kosmiczne</a></li>
            <li><a href="index.php?idp=misje">Misje Kosmiczne</a></li>
            <li><a href="index.php?idp=kontakt">Kontakt</a></li>
            <li><a href="index.php?idp=kolorujtlo">kolorujtlo</a></li>
            <li><a href="index.php?idp=timedate">timedate</a></li>
            <li><a href="index.php?idp=lab3">Lab 3</a></li>
            <li><a href="index.php?idp=filmy">Filmy</a></li>
        </ul>
    </nav>

    <!-- Wstawienie treści wybranej strony -->
    <?php
    if (file_exists($strona)) {
        include($strona);
    } else {
        echo "Strona nie została znaleziona.";
    }
    ?>

    <!-- Identyfikator projektu -->
    <?php
    $nr_indeksu = '164440';
    $nrGrupy = '5';
    echo 'Autor: Piotr Wielgolewski ' . $nr_indeksu . ' grupa ' . $nrGrupy . '<br /><br />';
    ?>
</body>
</html>