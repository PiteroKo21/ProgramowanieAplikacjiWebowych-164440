<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'moja_strona';

// Dane logowania do panelu admina
$login = 'admin';
$pass = 'admin123';

// Utworzenie połączenia
$conn = mysqli_connect($host, $user, $password, $database);

// Sprawdzenie połączenia
if (!$conn) {
    die("Błąd połączenia: " . mysqli_connect_error());
}

// Ustawienie kodowania znaków
mysqli_set_charset($conn, "utf8");
?>