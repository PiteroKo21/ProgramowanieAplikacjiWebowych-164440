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
                <button type="submit" name="edytuj" class="edit-btn">
                    <i class="fas fa-edit"></i> Edytuj
                </button>
                <button type="submit" name="usun" class="delete-btn" 
                    onclick="return confirm(\'Czy na pewno chcesz usunąć tę podstronę?\')">
                    <i class="fas fa-trash"></i> Usuń
                </button>
            </form>
        </td>';
        $wynik .= '</tr>';
    }
    
    $wynik .= '</tbody></table></div>';
    
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
    
    // Dodaj obsługę komunikatów
    if (isset($_GET['success'])) {
        switch ($_GET['success']) {
            case '1':
                echo '<p class="success">Operacja zakończona pomyślnie.</p>';
                break;
            case '2':
                echo '<p class="success">Podstrona została usunięta.</p>';
                break;
        }
    }
    
    if (isset($_POST['edytuj'])) {
        echo EdytujPodstrone();
    } elseif (isset($_POST['usun'])) {
        echo UsunPodstrone();
    } elseif (isset($_GET['action']) && $_GET['action'] == 'dodaj') {
        echo DodajNowaPodstrone();
    } else {
        echo ListaPodstron();
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
</body>
</html>