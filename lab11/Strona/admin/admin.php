<?php
session_start();
include('../html/cfg.php');
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Panel Administracyjny</title>
</head>
<body>
<?php
/**
 * Funkcja generująca formularz logowania
 */
function FormularzLogowania() {
    $wynik = '
    <div class="logowanie">
        <h1>Panel Administracyjny</h1>
        <form method="post" action="' . htmlspecialchars($_SERVER['REQUEST_URI']) . '">
            <label for="login">Login:</label>
            <input type="text" name="login" id="login" required><br>
            
            <label for="pass">Hasło:</label>
            <input type="password" name="pass" id="pass" required><br>
            
            <input type="submit" name="zaloguj" value="Zaloguj">
        </form>
    </div>';
    
    return $wynik;
}

/**
 * Funkcja wyświetlająca listę podstron
 * Używa globalnej zmiennej połączenia z bazą danych
 */
function ListaPodstron() {
    global $conn;
    
    // Dodajemy przycisk do dodawania nowej podstrony
    $wynik = '
    <div class="admin-controls">
        <button onclick="location.href=\'?action=dodaj\'" class="add-button">Dodaj nową podstronę</button>
        <button onclick="location.href=\'?action=produkty\'" class="add-button">Zarządzaj Produktami</button>
    </div>';
    
    $query = "SELECT * FROM page_list ORDER BY id ASC";
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        return '<p class="error">Błąd zapytania: ' . mysqli_error($conn) . '</p>';
    }
    
    $wynik .= '<div class="page-list">';
    $wynik .= '<h2>Lista Podstron</h2>';
    $wynik .= '<table class="admin-table">';
    $wynik .= '<thead>
                <tr>
                    <th>ID</th>
                    <th>Tytuł</th>
                    <th>Status</th>
                    <th>Akcje</th>
                </tr>
               </thead>
               <tbody>';
    
    while($row = mysqli_fetch_array($result)) {
        $wynik .= '<tr>';
        $wynik .= '<td>' . htmlspecialchars($row['id']) . '</td>';
        $wynik .= '<td>' . htmlspecialchars($row['page_title']) . '</td>';
        $wynik .= '<td>' . ($row['status'] == 1 ? 'Aktywna' : 'Nieaktywna') . '</td>';
        $wynik .= '<td class="action-buttons">
            <form method="post" style="display: inline;">
                <input type="hidden" name="id" value="' . $row['id'] . '">
                <button type="submit" name="edytuj" class="edit-btn">Edytuj</button>
                <button type="submit" name="usun" class="delete-btn" onclick="return confirm(\'Czy na pewno chcesz usunąć tę podstronę?\')">Usuń</button>
            </form>
        </td>';
        $wynik .= '</tr>';
    }
    
    $wynik .= '</tbody></table>';
    return $wynik;
}

/**
 * Funkcja do edytowania podstrony
 * Używa globalnej zmiennej połączenia z bazą danych
 */
function EdytujPodstrone() {
    global $conn;
    
    if (!isset($_POST['id'])) {
        return 'Błąd: nie przekazano ID podstrony do edycji.';
    }
    
    $id = (int)$_POST['id'];
    $query = "SELECT * FROM page_list WHERE id = $id LIMIT 1";
    $result = mysqli_query($conn, $query);
    
    if (!$result || mysqli_num_rows($result) == 0) {
        return 'Błąd: nie znaleziono podstrony o podanym ID.';
    }
    
    $row = mysqli_fetch_array($result);
    
    $wynik = '
    <div class="edit-form">
        <h2>Edycja Podstrony</h2>
        <form method="post" action="' . htmlspecialchars($_SERVER['REQUEST_URI']) . '">
            <input type="hidden" name="id" value="' . $row['id'] . '">
            
            <div class="form-group">
                <label for="page_title">Tytuł podstrony:</label>
                <input type="text" name="page_title" id="page_title" 
                    value="' . htmlspecialchars($row['page_title']) . '" required>
            </div>
            
            <div class="form-group">
                <label for="page_content">Treść podstrony:</label>
                <textarea name="page_content" id="page_content" rows="10" required>' . 
                    htmlspecialchars($row['page_content']) . 
                '</textarea>
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="status" value="1" ' . 
                    ($row['status'] == 1 ? 'checked' : '') . '>
                    Strona aktywna
                </label>
            </div>
            
            <div class="form-buttons">
                <input type="submit" name="zapisz" value="Zapisz zmiany" class="save-btn">
                <a href="' . $_SERVER['PHP_SELF'] . '" class="cancel-btn">Anuluj</a>
            </div>
        </form>
    </div>';
    
    return $wynik;
}

/**
 * Funkcja do dodawania nowej podstrony
 * Używa globalnej zmiennej połączenia z bazą danych
 */
function DodajNowaPodstrone() {
    global $conn;
    
    if (isset($_POST['dodaj'])) {
        $tytul = mysqli_real_escape_string($conn, $_POST['page_title']);
        $tresc = mysqli_real_escape_string($conn, $_POST['page_content']);
        $status = isset($_POST['status']) ? 1 : 0;
        
        $query = "INSERT INTO page_list (page_title, page_content, status) 
                 VALUES ('$tytul', '$tresc', $status)";
                 
        if (mysqli_query($conn, $query)) {
            header('Location: ' . $_SERVER['PHP_SELF'] . '?success=1');
            exit();
        } else {
            echo '<p class="error">Błąd podczas dodawania podstrony: ' . mysqli_error($conn) . '</p>';
        }
    }
    
    $wynik = '
    <div class="edit-form">
        <h2>Dodaj Nową Podstronę</h2>
        <form method="post" action="' . htmlspecialchars($_SERVER['REQUEST_URI']) . '">
            <div class="form-group">
                <label for="page_title">Tytuł podstrony:</label>
                <input type="text" name="page_title" id="page_title" required>
            </div>
            
            <div class="form-group">
                <label for="page_content">Treść podstrony:</label>
                <textarea name="page_content" id="page_content" rows="10" required></textarea>
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="status" value="1" checked>
                    Strona aktywna
                </label>
            </div>
            
            <div class="form-buttons">
                <input type="submit" name="dodaj" value="Dodaj podstronę" class="save-btn">
                <a href="' . $_SERVER['PHP_SELF'] . '" class="cancel-btn">Anuluj</a>
            </div>
        </form>
    </div>';
    
    return $wynik;
}

/**
 * Funkcja do usuwania podstrony
 * Używa globalnej zmiennej połączenia z bazą danych
 */
function UsunPodstrone() {
    global $conn;
    
    if (!isset($_POST['id'])) {
        return 'Błąd: nie przekazano ID podstrony do usunięcia.';
    }
    
    $id = (int)$_POST['id'];
    $query = "DELETE FROM page_list WHERE id = $id LIMIT 1";
    
    if (mysqli_query($conn, $query)) {
        header('Location: ' . $_SERVER['PHP_SELF'] . '?success=2');
        exit();
    } else {
        return '<p class="error">Błąd podczas usuwania podstrony: ' . mysqli_error($conn) . '</p>';
    }
}

/**
 * Funkcja do dodawania nowej kategorii
 * Używa globalnej zmiennej połączenia z bazą danych
 */
function DodajKategorie() {
    global $conn;
    
    if (isset($_POST['nazwa_kategorii'])) {
        $nazwa = mysqli_real_escape_string($conn, $_POST['nazwa_kategorii']);
        $rodzic = (int)$_POST['rodzic_kategorii'];
        
        // Sprawdzamy czy kategoria nadrzędna istnieje (jeśli została wybrana)
        if ($rodzic > 0) {
            $check_query = "SELECT id FROM kategorie WHERE id = $rodzic";
            $check_result = mysqli_query($conn, $check_query);
            if (mysqli_num_rows($check_result) == 0) {
                return '<div class="error">Wybrana kategoria nadrzędna nie istnieje.</div>';
            }
        }
        
        // Jeśli rodzic to 0, ustawiamy NULL
        $rodzic_sql = $rodzic > 0 ? $rodzic : "NULL";
        
        $query = "INSERT INTO kategorie (nazwa, matka) VALUES ('$nazwa', $rodzic_sql)";
        if (mysqli_query($conn, $query)) {
            return '<div class="success">Kategoria została dodana pomyślnie.</div>';
        } else {
            return '<div class="error">Błąd podczas dodawania kategorii: ' . mysqli_error($conn) . '</div>';
        }
    }
}

/**
 * Funkcja do usuwania kategorii
 * Używa globalnej zmiennej połączenia z bazą danych
 */
function UsunKategorie() {
    global $conn;
    
    if (isset($_POST['kategoria_id'])) {
        $id = (int)$_POST['kategoria_id'];
        
        // Aktualizuj podkategorie - ustaw ich rodzica na NULL
        $update_query = "UPDATE kategorie SET matka = NULL WHERE matka = $id";
        mysqli_query($conn, $update_query);
        
        // Usuń kategorię
        $query = "DELETE FROM kategorie WHERE id = $id LIMIT 1";
        if (mysqli_query($conn, $query)) {
            $_SESSION['komunikat'] = array(
                'typ' => 'success',
                'tresc' => 'Kategoria została usunięta pomyślnie.'
            );
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $_SESSION['komunikat'] = array(
                'typ' => 'error',
                'tresc' => 'Błąd podczas usuwania kategorii: ' . mysqli_error($conn)
            );
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
        }
    }
}

/**
 * Funkcja do edytowania kategorii
 * Używa globalnej zmiennej połączenia z bazą danych
 */
function EdytujKategorie() {
    global $conn;
    
    if (isset($_POST['edytuj_kategorie_submit'])) {
        $id = (int)$_POST['kategoria_id'];
        $nazwa = mysqli_real_escape_string($conn, $_POST['nazwa_kategorii']);
        $rodzic = (int)$_POST['rodzic_kategorii'];
        
        // Sprawdź czy nie próbujemy przenieść kategorii do jej własnej podkategorii
        if ($id == $rodzic) {
            return '<div class="error">Kategoria nie może być swoim własnym rodzicem.</div>';
        }
        
        // Sprawdź czy nowy rodzic nie jest podkategorią edytowanej kategorii
        $query = "WITH RECURSIVE subcategories AS (
            SELECT id, matka FROM kategorie WHERE id = $id
            UNION ALL
            SELECT k.id, k.matka FROM kategorie k
            INNER JOIN subcategories s ON k.matka = s.id
        )
        SELECT COUNT(*) as count FROM subcategories WHERE id = $rodzic";
        
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);
        
        if ($row['count'] > 0) {
            return '<div class="error">Nie można przenieść kategorii do jej własnej podkategorii.</div>';
        }
        
        // Jeśli rodzic to 0, ustawiamy NULL
        $rodzic_sql = $rodzic > 0 ? $rodzic : "NULL";
        
        $query = "UPDATE kategorie SET nazwa = '$nazwa', matka = $rodzic_sql WHERE id = $id";
        if (mysqli_query($conn, $query)) {
            return '<div class="success">Kategoria została zaktualizowana pomyślnie.</div>';
        } else {
            return '<div class="error">Błąd podczas aktualizacji kategorii: ' . mysqli_error($conn) . '</div>';
        }
    }
    
    // Formularz edycji
    if (isset($_POST['edytuj_kategorie'])) {
        $id = (int)$_POST['kategoria_id'];
        $query = "SELECT * FROM kategorie WHERE id = $id";
        $result = mysqli_query($conn, $query);
        $kategoria = mysqli_fetch_assoc($result);
        
        return '
        <div class="category-form">
            <h3>Edytuj kategorię</h3>
            <form method="post" action="">
                <input type="hidden" name="kategoria_id" value="' . $id . '">
                <div class="form-group">
                    <label for="nazwa_kategorii">Nazwa kategorii:</label>
                    <input type="text" id="nazwa_kategorii" name="nazwa_kategorii" 
                           value="' . htmlspecialchars($kategoria['nazwa']) . '" required>
                </div>
                <div class="form-group">
                    <label for="rodzic_kategorii">Kategoria nadrzędna:</label>
                    <select id="rodzic_kategorii" name="rodzic_kategorii">
                        <option value="0">Brak (kategoria główna)</option>
                        ' . PobierzOpcjeKategorii($kategoria['matka'], $id) . '
                    </select>
                </div>
                <div class="form-buttons">
                    <button type="submit" name="edytuj_kategorie_submit" class="save-btn">Zapisz zmiany</button>
                    <a href="' . $_SERVER['PHP_SELF'] . '" class="cancel-btn">Anuluj</a>
                </div>
            </form>
        </div>';
    }
}

/**
 * Funkcja do wyświetlania kategorii jako opcji w formularzu
 */
function PokazKategorieOpcje($matka = 0, $prefix = '') {
    global $conn;
    $wynik = '';
    $query = "SELECT * FROM kategorie WHERE matka = $matka";
    $result = mysqli_query($conn, $query);

    while ($row = mysqli_fetch_array($result)) {
        $wynik .= '<option value="' . $row['id'] . '">' . $prefix . htmlspecialchars($row['nazwa']) . '</option>';
        $wynik .= PokazKategorieOpcje($row['id'], $prefix . '--');
    }

    return $wynik;
}

/**
 * Funkcja do wyświetlania wszystkich kategorii
 */
function PokazKategorie() {
    global $conn;
    $wynik = '<h2>Lista Kategorii</h2>';
    $wynik .= '<table class="admin-table"><thead><tr><th>ID</th><th>Nazwa</th><th>Matka</th><th>Akcje</th></tr></thead><tbody>';

    $query = "SELECT * FROM kategorie WHERE matka = 0";
    $result = mysqli_query($conn, $query);

    while ($row = mysqli_fetch_array($result)) {
        $wynik .= '<tr>';
        $wynik .= '<td>' . htmlspecialchars($row['id']) . '</td>';
        $wynik .= '<td>' . htmlspecialchars($row['nazwa']) . '</td>';
        $wynik .= '<td>Brak</td>';
        $wynik .= '<td><form method="post" style="display:inline;">
                        <input type="hidden" name="id" value="' . $row['id'] . '">
                        <button type="submit" name="edytuj_kategorie" class="edit-btn">Edytuj</button>
                        <button type="submit" name="usun_kategorie" class="delete-btn" onclick="return confirm(\'Czy na pewno chcesz usunąć tę kategorię?\')">Usuń</button>
                    </form></td>';
        $wynik .= '</tr>';
        $wynik .= PokazPodkategorie($row['id']);
    }

    $wynik .= '</tbody></table>';
    return $wynik;
}

/**
 * Funkcja do wyświetlania podkategorii
 */
function PokazPodkategorie($matka) {
    global $conn;
    $wynik = '';
    $query = "SELECT * FROM kategorie WHERE matka = $matka";
    $result = mysqli_query($conn, $query);

    while ($row = mysqli_fetch_array($result)) {
        $wynik .= '<tr>';
        $wynik .= '<td>' . htmlspecialchars($row['id']) . '</td>';
        $wynik .= '<td>' . htmlspecialchars($row['nazwa']) . '</td>';
        $wynik .= '<td>' . htmlspecialchars($matka) . '</td>';
        $wynik .= '<td><form method="post" style="display:inline;">
                        <input type="hidden" name="id" value="' . $row['id'] . '">
                        <button type="submit" name="edytuj_kategorie" class="edit-btn">Edytuj</button>
                        <button type="submit" name="usun_kategorie" class="delete-btn" onclick="return confirm(\'Czy na pewno chcesz usunąć tę podkategorię?\')">Usuń</button>
                    </form></td>';
        $wynik .= '</tr>';
    }

    return $wynik;
}

// Funkcje do zarządzania produktami
function DodajProdukt() {
    global $conn;

    if (isset($_POST['dodaj_produkt_submit'])) {
        if (empty($_POST['tytul']) || empty($_POST['opis']) || empty($_POST['cena_netto']) || 
            empty($_POST['podatek_vat']) || empty($_POST['ilosc_dostepnych'])) {
            return '<p class="error">Wypełnij wszystkie wymagane pola!</p>';
        }

        try {
            $tytul = mysqli_real_escape_string($conn, $_POST['tytul']);
            $opis = mysqli_real_escape_string($conn, $_POST['opis']);
            $data_wygasniecia = mysqli_real_escape_string($conn, $_POST['data_wygasniecia']);
            $cena_netto = (float)$_POST['cena_netto'];
            $podatek_vat = (float)$_POST['podatek_vat'];
            $ilosc_dostepnych = (int)$_POST['ilosc_dostepnych'];
            $status_dostepnosci = mysqli_real_escape_string($conn, $_POST['status_dostepnosci']);
            $kategoria = (int)$_POST['kategoria'];
            $gabaryt = mysqli_real_escape_string($conn, $_POST['gabaryt']);

            // Sprawdzamy czy jest zdjęcie
            if (isset($_FILES['zdjecie']) && $_FILES['zdjecie']['size'] > 0) {
                $zdjecie = file_get_contents($_FILES['zdjecie']['tmp_name']);
                $query = "INSERT INTO produkty (tytul, opis, data_wygasniecia, cena_netto, podatek_vat, 
                                             ilosc_dostepnych, status_dostepnosci, kategoria, gabaryt, zdjecie) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = mysqli_prepare($conn, $query);
                if (!$stmt) {
                    throw new Exception('Błąd przygotowania zapytania: ' . mysqli_error($conn));
                }

                // Bindowanie parametrów ze zdjęciem
                mysqli_stmt_bind_param($stmt, 'sssddisissb', $tytul, $opis, $data_wygasniecia, 
                                     $cena_netto, $podatek_vat, $ilosc_dostepnych, 
                                     $status_dostepnosci, $kategoria, $gabaryt, $zdjecie);
            } else {
                // Zapytanie bez zdjęcia
                $query = "INSERT INTO produkty (tytul, opis, data_wygasniecia, cena_netto, podatek_vat, 
                                             ilosc_dostepnych, status_dostepnosci, kategoria, gabaryt) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = mysqli_prepare($conn, $query);
                if (!$stmt) {
                    throw new Exception('Błąd przygotowania zapytania: ' . mysqli_error($conn));
                }

                // Bindowanie parametrów bez zdjęcia
                mysqli_stmt_bind_param($stmt, 'sssddisis', $tytul, $opis, $data_wygasniecia, 
                                     $cena_netto, $podatek_vat, $ilosc_dostepnych, 
                                     $status_dostepnosci, $kategoria, $gabaryt);
            }

            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception('Błąd wykonania zapytania: ' . mysqli_stmt_error($stmt));
            }

            header('Location: admin.php?action=produkty&success=4');
            exit();

        } catch (Exception $e) {
            return '<p class="error">Błąd: ' . $e->getMessage() . '</p>';
        }
    }

    // Reszta kodu formularza pozostaje bez zmian
    return '
    <div class="admin-form">
        <div class="form-header">
            <h2><i class="fas fa-plus"></i> Dodaj nowy produkt</h2>
        </div>
        <form method="post" enctype="multipart/form-data" class="product-form">
            <div class="form-group">
                <label for="tytul">Tytuł produktu:</label>
                <input type="text" id="tytul" name="tytul" required>
            </div>
            
            <div class="form-group">
                <label for="opis">Opis produktu:</label>
                <textarea id="opis" name="opis" rows="5" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="data_wygasniecia">Data wygaśnięcia:</label>
                <input type="date" id="data_wygasniecia" name="data_wygasniecia" required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="cena_netto">Cena netto:</label>
                    <input type="number" step="0.01" id="cena_netto" name="cena_netto" required>
                </div>
                
                <div class="form-group">
                    <label for="podatek_vat">Podatek VAT (%):</label>
                    <input type="number" step="0.01" id="podatek_vat" name="podatek_vat" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="ilosc_dostepnych">Ilość dostępnych:</label>
                    <input type="number" id="ilosc_dostepnych" name="ilosc_dostepnych" required>
                </div>
                
                <div class="form-group">
                    <label for="status_dostepnosci">Status dostępności:</label>
                    <select id="status_dostepnosci" name="status_dostepnosci" required>
                        <option value="dostepny">Dostępny</option>
                        <option value="niedostepny">Niedostępny</option>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="kategoria">Kategoria:</label>
                    <select id="kategoria" name="kategoria" required>
                        ' . PobierzOpcjeKategorii() . '
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="gabaryt">Gabaryt:</label>
                    <input type="text" id="gabaryt" name="gabaryt" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="zdjecie">Zdjęcie produktu:</label>
                <input type="file" id="zdjecie" name="zdjecie" accept="image/*">
            </div>
            
            <div class="form-buttons">
                <button type="submit" name="dodaj_produkt_submit" class="submit-btn">
                    <i class="fas fa-save"></i> Zapisz produkt
                </button>
                <a href="admin.php?action=produkty" class="cancel-btn">
                    <i class="fas fa-times"></i> Anuluj
                </a>
            </div>
        </form>
    </div>';
}

/**
 * Funkcja do usuwania produktu
 */
function UsunProdukt() {
    global $conn;

    if (!isset($_POST['id'])) {
        return 'Błąd: nie przekazano ID produktu do usunięcia.';
    }

    $id = (int)$_POST['id'];
    $query = "DELETE FROM produkty WHERE id = $id LIMIT 1";

    if (mysqli_query($conn, $query)) {
        header('Location: ' . $_SERVER['PHP_SELF'] . '?success=5');
        exit();
    } else {
        return '<p class="error">Błąd podczas usuwania produktu: ' . mysqli_error($conn) . '</p>';
    }
}

/**
 * Funkcja do edytowania produktu
 */
function EdytujProdukt() {
    global $conn;

    if (!isset($_POST['id'])) {
        return 'Błąd: nie przekazano ID produktu do edycji.';
    }

    $id = (int)$_POST['id'];
    $query = "SELECT * FROM produkty WHERE id = $id LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (!$result || mysqli_num_rows($result) == 0) {
        return 'Błąd: nie znaleziono produktu o podanym ID.';
    }

    $row = mysqli_fetch_array($result);

    // Formularz edytowania produktu
    $wynik = '
    <div class="edit-form">
        <h2>Edycja Produktu</h2>
        <form method="post" action="' . htmlspecialchars($_SERVER['REQUEST_URI']) . '" enctype="multipart/form-data">
            <input type="hidden" name="id" value="' . $row['id'] . '">
            <div class="form-group">
                <label for="tytul">Tytuł produktu:</label>
                <input type="text" name="tytul" id="tytul" value="' . htmlspecialchars($row['tytul']) . '" required>
            </div>
            <div class="form-group">
                <label for="opis">Opis produktu:</label>
                <textarea name="opis" id="opis" rows="5" required>' . htmlspecialchars($row['opis']) . '</textarea>
            </div>
            <div class="form-group">
                <label for="data_wygasniecia">Data wygaśnięcia:</label>
                <input type="date" name="data_wygasniecia" id="data_wygasniecia" value="' . htmlspecialchars($row['data_wygasniecia']) . '" required>
            </div>
            <div class="form-group">
                <label for="cena_netto">Cena netto:</label>
                <input type="number" step="0.01" name="cena_netto" id="cena_netto" value="' . htmlspecialchars($row['cena_netto']) . '" required>
            </div>
            <div class="form-group">
                <label for="podatek_vat">Podatek VAT (%):</label>
                <input type="number" step="0.01" name="podatek_vat" id="podatek_vat" value="' . htmlspecialchars($row['podatek_vat']) . '" required>
            </div>
            <div class="form-group">
                <label for="ilosc_dostepnych">Ilość dostępnych:</label>
                <input type="number" name="ilosc_dostepnych" id="ilosc_dostepnych" value="' . htmlspecialchars($row['ilosc_dostepnych']) . '" required>
            </div>
            <div class="form-group">
                <label for="status_dostepnosci">Status dostępności:</label>
                <select name="status_dostepnosci" id="status_dostepnosci">
                    <option value="dostepny"' . ($row['status_dostepnosci'] == 'dostepny' ? ' selected' : '') . '>Dostępny</option>
                    <option value="niedostepny"' . ($row['status_dostepnosci'] == 'niedostepny' ? ' selected' : '') . '>Niedostępny</option>
                </select>
            </div>
            <div class="form-group">
                <label for="kategoria">Kategoria:</label>
                <input type="number" name="kategoria" id="kategoria" value="' . htmlspecialchars($row['kategoria']) . '" required>
            </div>
            <div class="form-group">
                <label for="gabaryt">Gabaryt:</label>
                <input type="text" name="gabaryt" id="gabaryt" value="' . htmlspecialchars($row['gabaryt']) . '" required>
            </div>
            <div class="form-group">
                <label for="zdjecie">Zdjęcie:</label>
                <input type="file" name="zdjecie" id="zdjecie">
            </div>
            <div class="form-buttons">
                <input type="submit" name="edytuj_produkt" value="Zapisz zmiany" class="save-btn">
                <a href="' . $_SERVER['PHP_SELF'] . '" class="cancel-btn">Anuluj</a>
            </div>
        </form>
    </div>';

    return $wynik;
}

/**
 * Funkcja do wyświetlania produktów
 */
function PokazProdukty() {
    global $conn;
    $wynik = '
    <div class="admin-section">
        <div class="section-header">
            <h2>Lista Produktów</h2>
            <a href="?action=produkty&add=1" class="add-button">
                <i class="fas fa-plus"></i> Dodaj nowy produkt
            </a>
        </div>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tytuł</th>
                    <th>Cena netto</th>
                    <th>VAT</th>
                    <th>Status</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>';

    $query = "SELECT * FROM produkty ORDER BY id DESC";
    $result = mysqli_query($conn, $query);

    while ($row = mysqli_fetch_array($result)) {
        $wynik .= '<tr>
            <td>' . htmlspecialchars($row['id']) . '</td>
            <td>' . htmlspecialchars($row['tytul']) . '</td>
            <td>' . number_format($row['cena_netto'], 2) . ' PLN</td>
            <td>' . $row['podatek_vat'] . '%</td>
            <td>' . htmlspecialchars($row['status_dostepnosci']) . '</td>
            <td class="action-buttons">
                <form method="post" style="display:inline;">
                    <input type="hidden" name="id" value="' . $row['id'] . '">
                    <button type="submit" name="edytuj_produkt" class="edit-btn" title="Edytuj">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="submit" name="usun_produkt" class="delete-btn" 
                        onclick="return confirm(\'Czy na pewno chcesz usunąć ten produkt?\')" title="Usuń">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </td>
        </tr>';
    }

    $wynik .= '</tbody></table></div>';
    return $wynik;
}

// Dodaj nową funkcję do wyświetlania drzewa kategorii
function WyswietlDrzewoKategorii() {
    global $conn;
    
    $wynik = '
    <div class="category-tree-container">
        <h2>Zarządzanie Kategoriami</h2>
        <div class="category-actions">
            <button onclick="pokazFormularzKategorii()" class="add-button">
                <i class="fas fa-plus"></i> Dodaj kategorię
            </button>
        </div>
        
        <div id="formularz-kategorii" style="display:none;" class="category-form">
            <h3>Dodaj nową kategorię</h3>
            <form method="post" action="">
                <div class="form-group">
                    <label for="nazwa_kategorii">Nazwa kategorii:</label>
                    <input type="text" id="nazwa_kategorii" name="nazwa_kategorii" required>
                </div>
                <div class="form-group">
                    <label for="rodzic_kategorii">Kategoria nadrzędna:</label>
                    <select id="rodzic_kategorii" name="rodzic_kategorii">
                        <option value="0">Brak (kategoria główna)</option>
                        ' . PobierzOpcjeKategorii() . '
                    </select>
                </div>
                <div class="form-buttons">
                    <button type="submit" name="dodaj_kategorie" class="save-btn">
                        <i class="fas fa-save"></i> Zapisz
                    </button>
                    <button type="button" onclick="ukryjFormularzKategorii()" class="cancel-btn">
                        <i class="fas fa-times"></i> Anuluj
                    </button>
                </div>
            </form>
        </div>
        
        <div class="category-tree">
            ' . PobierzDrzewoKategorii() . '
        </div>
    </div>
    
    <script>
    function pokazFormularzKategorii() {
        document.getElementById("formularz-kategorii").style.display = "block";
    }
    
    function ukryjFormularzKategorii() {
        document.getElementById("formularz-kategorii").style.display = "none";
    }

    function dodajPodkategorie(rodzicId) {
        const form = document.getElementById("formularz-kategorii");
        const select = document.getElementById("rodzic_kategorii");
        form.style.display = "block";
        select.value = rodzicId;
    }
    </script>';
    
    return $wynik;
}

function PobierzDrzewoKategorii($rodzic = 0, $poziom = 0) {
    global $conn;
    $wynik = '<ul class="category-list">';
    
    $query = "SELECT * FROM kategorie WHERE matka " . ($rodzic === 0 ? "IS NULL" : "= $rodzic") . " ORDER BY nazwa";
    $result = mysqli_query($conn, $query);
    
    while ($row = mysqli_fetch_assoc($result)) {
        $wynik .= '<li class="category-item">
            <div class="category-content">
                <span class="category-name">' . str_repeat('—', $poziom) . ' ' . htmlspecialchars($row['nazwa']) . '</span>
                <div class="category-actions">
                    <button onclick="dodajPodkategorie(' . $row['id'] . ')" class="add-subcategory-btn" title="Dodaj podkategorię">
                        <i class="fas fa-plus-circle"></i>
                    </button>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="kategoria_id" value="' . $row['id'] . '">
                        <button type="submit" name="edytuj_kategorie" class="edit-btn" title="Edytuj kategorię">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="submit" name="usun_kategorie" class="delete-btn" 
                            onclick="return confirm(\'Czy na pewno chcesz usunąć kategorię ' . htmlspecialchars($row['nazwa']) . ' i wszystkie jej podkategorie?\')"
                            title="Usuń kategorię">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>';
        
        // Rekurencyjne pobieranie podkategorii
        $wynik .= PobierzDrzewoKategorii($row['id'], $poziom + 1);
        $wynik .= '</li>';
    }
    
    $wynik .= '</ul>';
    return $wynik;
}

// Dodaj nowe funkcje do obsługi kategorii
function PobierzOpcjeKategorii($selected = 0, $wylacz_id = null, $rodzic = null, $poziom = 0) {
    global $conn;
    $wynik = '';
    
    // Zmodyfikowane zapytanie dla obsługi NULL
    $query = "SELECT * FROM kategorie WHERE " . 
             ($rodzic === null ? "matka IS NULL" : "matka = $rodzic") . 
             " ORDER BY nazwa";
             
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        return '<option value="">Błąd: ' . mysqli_error($conn) . '</option>';
    }
    
    while ($row = mysqli_fetch_array($result)) {
        // Pomijamy kategorię, której nie chcemy pokazywać (np. podczas edycji)
        if ($row['id'] != $wylacz_id) {
            $wynik .= '<option value="' . $row['id'] . '"' . 
                     ($selected == $row['id'] ? ' selected' : '') . '>' .
                     str_repeat('— ', $poziom) . htmlspecialchars($row['nazwa']) .
                     '</option>';
            
            // Rekurencyjnie pobieramy podkategorie
            $wynik .= PobierzOpcjeKategorii($selected, $wylacz_id, $row['id'], $poziom + 1);
        }
    }
    
    return $wynik;
}

// Główna logika panelu administracyjnego
if (isset($_POST['zaloguj'])) {
    if ($_POST['login'] === $login && $_POST['pass'] === $pass) {
        $_SESSION['zalogowany'] = true;
    } else {
        echo '<p style="color: red;">Błędny login lub hasło!</p>';
    }
}

if (isset($_POST['wyloguj'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Dodaj na początku pliku, przed wyświetlaniem zawartości
if (isset($_POST['zapisz']) && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $tytul = mysqli_real_escape_string($conn, $_POST['page_title']);
    $tresc = mysqli_real_escape_string($conn, $_POST['page_content']);
    $status = isset($_POST['status']) ? 1 : 0;
    
    $query = "UPDATE page_list SET 
              page_title = '$tytul',
              page_content = '$tresc',
              status = $status
              WHERE id = $id";
              
    if (mysqli_query($conn, $query)) {
        header('Location: ' . $_SERVER['PHP_SELF'] . '?success=1');
        exit();
    } else {
        echo '<p class="error">Błąd podczas zapisywania zmian: ' . mysqli_error($conn) . '</p>';
    }
}

// Wyświetlanie odpowiedniej zawartości
if (isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] === true) {
    echo '<div class="admin-panel">';
    echo '<form method="post" style="float: right;">
            <input type="submit" name="wyloguj" value="Wyloguj">
          </form>';
    echo '<h1>Panel Administracyjny</h1>';
    
    // Wyświetl komunikat jeśli istnieje i od razu go usuń
    if (isset($_SESSION['komunikat'])) {
        echo '<div class="' . $_SESSION['komunikat']['typ'] . ' message-fade">' . 
             $_SESSION['komunikat']['tresc'] . 
             '</div>';
        unset($_SESSION['komunikat']); // Usuń komunikat po wyświetleniu
    }
    
    // Dodaj obsługę komunikatów
    if (isset($_GET['success'])) {
        switch ($_GET['success']) {
            case '1':
                echo '<p class="success">Operacja zakończona pomyślnie.</p>';
                break;
            case '2':
                echo '<p class="success">Podstrona została usunięta.</p>';
                break;
            case '3':
                echo '<p class="success">Kategoria została dodana.</p>';
                break;
            case '4':
                echo '<p class="success">Produkt został dodany.</p>';
                break;
            case '5':
                echo '<p class="success">Produkt został usunięty.</p>';
                break;
        }
    }
    
    // Obsługa kategorii
    if (isset($_POST['dodaj_kategorie'])) {
        echo DodajKategorie();
    } elseif (isset($_POST['usun_kategorie'])) {
        echo UsunKategorie();
    }
    
    // Wyświetl drzewo kategorii
    $edycja_kategorii = EdytujKategorie();
    if ($edycja_kategorii) {
        echo $edycja_kategorii;
    }
    
    // Wyświetl odpowiednią sekcję w zależności od akcji
    if (isset($_POST['edytuj'])) {
        echo EdytujPodstrone();
    } elseif (isset($_POST['usun'])) {
        echo UsunPodstrone();
    } elseif (isset($_GET['action']) && $_GET['action'] == 'dodaj') {
        echo DodajNowaPodstrone();
    } elseif (isset($_GET['action']) && $_GET['action'] == 'produkty') {
        if (isset($_GET['add'])) {
            echo DodajProdukt();
        } elseif (isset($_POST['usun_produkt'])) {
            echo UsunProdukt();
        } elseif (isset($_POST['edytuj_produkt'])) {
            echo EdytujProdukt();
        } else {
            echo PokazProdukty();
        }
    } else {
        // Strona główna panelu - wyświetl listę podstron i drzewo kategorii
        echo ListaPodstron();
        echo '<div class="admin-section">';
        echo WyswietlDrzewoKategorii();
        echo '</div>';
    }
    
    echo '</div>';
} else {
    echo FormularzLogowania();
}

// Dodaj style dla komunikatów sukcesu (w sekcji ze stylami)
echo '
<style>
    .success {
        color: #28a745;
        background: #d4edda;
        border: 1px solid #c3e6cb;
        padding: 10px;
        margin: 10px 0;
        border-radius: 4px;
    }
</style>';
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Automatyczne usuwanie komunikatów po 5 sekundach
    const messages = document.querySelectorAll('.message-fade');
    if (messages.length > 0) {
        setTimeout(function() {
            messages.forEach(function(message) {
                message.style.opacity = '0';
                setTimeout(function() {
                    message.remove();
                }, 1000);
            });
        }, 5000);
    }
});
</script>
</body>
</html>