<?php
$host = 'localhost';
$user = 'root';  // domyślny użytkownik w XAMPP
$password = '';   // domyślnie puste hasło w XAMPP
$database = 'moja_strona';

// Utworzenie połączenia
$conn = mysqli_connect($host, $user, $password, $database);

// Sprawdzenie połączenia
if (!$conn) {
    die("Błąd połączenia: " . mysqli_connect_error());
}

// Ustawienie kodowania znaków
mysqli_set_charset($conn, "utf8");
?>