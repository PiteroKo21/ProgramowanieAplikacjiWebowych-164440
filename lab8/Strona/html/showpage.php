<?php
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$baza = 'moja_strona';
$link = mysqli_connect($dbhost, $dbuser, $dbpass, $baza);

if (!$link) {
    die("Connection failed: " . mysqli_connect_error());
}
function PokazPodstrone($id) {
    global $link;
    
    // Sprawdzenie czy $id jest liczbą
    $id = (int)$id;
    
    // Debugowanie - sprawdź wartość ID
    // echo "Debug: ID = " . $id . "<br>";
    
    // Zapytanie do bazy
    $query = "SELECT * FROM page_list WHERE id = '$id' LIMIT 1";
    $result = mysqli_query($link, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);
        echo $row['page_content'];
    } else {
        // Wyświetl błąd lub domyślną treść
        echo 'Nie znaleziono strony o podanym ID: ' . $id;
        // Możesz też wyświetlić błąd MySQL dla celów debugowania:
        // echo "<br>Błąd MySQL: " . mysqli_error($link);
    }
}

// Sprawdź, czy ID jest przekazane w URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    // Jeśli nie ma ID, ustaw domyślne (np. strona główna)
    $id = 2;
}

// Wywołaj funkcję
PokazPodstrone($id);
?> 