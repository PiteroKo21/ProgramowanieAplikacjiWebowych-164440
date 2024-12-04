<?php
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$baza = 'moja_strona';
$link = mysqli_connect($dbhost, $dbuser, $dbpass, $baza);

if (!$link) {
    die("Connection failed: " . mysqli_connect_error());
}

// Nagłówek dla funkcji PokazPodstrone
/**
 * Funkcja do wyświetlania podstrony na podstawie ID
 */
function PokazPodstrone($id) {
    global $link; // Użycie globalnej zmiennej połączenia z bazą danych
    
    // Sprawdzenie czy $id jest liczbą
    $id = (int)$id;
    
    // Zapytanie do bazy o podstronę na podstawie ID
    $query = "SELECT * FROM page_list WHERE id = '$id' LIMIT 1";
    $result = mysqli_query($link, $query); // Wykonanie zapytania
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);
        echo $row['page_content']; // Wyświetlenie treści podstrony
    } else {
        // Wyświetl błąd lub domyślną treść
        echo 'Nie znaleziono strony o podanym ID: ' . $id;
    }
}

// Sprawdź, czy ID jest przekazane w URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    $id = 2; // Domyślne ID
}

// Wywołaj funkcję
PokazPodstrone($id); // Wywołanie funkcji do wyświetlenia podstrony
?> 