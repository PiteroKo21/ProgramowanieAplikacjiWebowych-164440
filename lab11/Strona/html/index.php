<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
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
            <li><a href="?id=1">Strona Główna</a></li>
            <li><a href="?id=2">Pionierzy Kosmosu</a></li>
            <li><a href="?id=3">Technologie Kosmiczne</a></li>
            <li><a href="?id=4">Misje Kosmiczne</a></li>
            <li><a href="?id=5">Kontakt</a></li>
            <li><a href="?id=6">kolorujtlo</a></li>
            <li><a href="?id=7">timedate</a></li>
            <li><a href="?id=8">Lab 3</a></li>
            <li><a href="?id=9">Filmy</a></li>
        </ul>
    </nav>
    
    <!-- Wstawienie treści wybranej strony -->
    
        <?php
        // Pobierz ID z URL
        $id = isset($_GET['id']) ? $_GET['id'] : 1;
        include('showpage.php');
        ?>
  
    <?php
    $nr_indeksu = '164440';
    $nrGrupy = '5';
    echo 'Autor: Piotr Wielgolewski ' . $nr_indeksu . ' grupa ' . $nrGrupy . '<br /><br />';
    ?>
</body>
</html>