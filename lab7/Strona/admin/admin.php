<?php
session_start();
include('../html/cfg.php');

function FormularzLogowania() {
    $wynik = '
    <div class="logowanie">
        <h1>Panel Administracyjny</h1>
        <form method="post" action="' . $_SERVER['REQUEST_URI'] . '">
            <label for="login">Login:</label>
            <input type="text" name="login" id="login" required><br>
            
            <label for="pass">Hasło:</label>
            <input type="password" name="pass" id="pass" required><br>
            
            <input type="submit" name="zaloguj" value="Zaloguj">
        </form>
    </div>';
    
    return $wynik;
}

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
    
    // Dodajemy style CSS dla nowych elementów
    $wynik .= '
    <style>
        .admin-controls {
            margin: 20px 0;
        }
        
        .add-button {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .admin-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        
        .admin-table th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #dee2e6;
        }
        
        .admin-table td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .action-buttons button {
            margin: 0 5px;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .edit-btn {
            background-color: #007bff;
            color: white;
        }
        
        .delete-btn {
            background-color: #dc3545;
            color: white;
        }
        
        .error {
            color: #dc3545;
            padding: 10px;
            margin: 10px 0;
            background: #f8d7da;
            border-radius: 4px;
        }
    </style>';
    
    return $wynik;
}

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
        <form method="post" action="' . $_SERVER['REQUEST_URI'] . '">
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
    </div>
    
    <style>
        .edit-form {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-group input[type="text"],
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 200px;
        }
        
        .form-buttons {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }
        
        .save-btn {
            padding: 10px 20px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .cancel-btn {
            padding: 10px 20px;
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            text-align: center;
        }
        
        .save-btn:hover {
            background: #218838;
        }
        
        .cancel-btn:hover {
            background: #c82333;
        }
    </style>';
    
    return $wynik;
}

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
        <form method="post" action="' . $_SERVER['REQUEST_URI'] . '">
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

echo '
<style>
    body {
        font-family: "Arial", sans-serif;
        background: #0b0d17;
        color: #ffffff;
        margin: 0;
        padding: 0;
    }

    .logowanie {
        max-width: 400px;
        margin: 50px auto;
        padding: 30px;
        background: linear-gradient(135deg, #1b1f3b, #2f365f);
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    .logowanie h1 {
        color: #f39c12;
        text-align: center;
        margin-bottom: 30px;
    }

    .logowanie form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .logowanie label {
        color: #bbbbbb;
    }

    .logowanie input[type="text"],
    .logowanie input[type="password"] {
        padding: 12px;
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 4px;
        color: white;
        font-size: 16px;
    }

    .logowanie input[type="submit"],
    .admin-panel button,
    .action-buttons button {
        padding: 12px 20px;
        background: #f39c12;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .logowanie input[type="submit"]:hover,
    .admin-panel button:hover,
    .action-buttons button:hover {
        background: #d87f0a;
    }

    .admin-panel {
        padding: 30px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .admin-panel h1 {
        color: #f39c12;
        text-align: center;
        margin-bottom: 30px;
    }

    .admin-controls {
        margin: 20px 0;
        text-align: right;
    }

    .add-button {
        background: #f39c12;
        padding: 12px 24px;
    }

    .admin-table {
        width: 100%;
        background: linear-gradient(135deg, #1b1f3b, #2f365f);
        border-radius: 8px;
        overflow: hidden;
        margin-top: 20px;
    }

    .admin-table th {
        background: rgba(243,156,18,0.2);
        color: #f39c12;
        padding: 15px;
        text-align: left;
    }

    .admin-table td {
        padding: 15px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        color: #bbbbbb;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
    }

    .edit-btn {
        background: #3498db;
    }

    .delete-btn {
        background: #e74c3c;
    }

    .edit-form {
        max-width: 800px;
        margin: 20px auto;
        padding: 30px;
        background: linear-gradient(135deg, #1b1f3b, #2f365f);
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    .edit-form h2 {
        color: #f39c12;
        text-align: center;
        margin-bottom: 30px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #bbbbbb;
    }

    .form-group input[type="text"],
    .form-group textarea {
        width: 100%;
        padding: 12px;
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 4px;
        color: white;
        font-size: 14px;
    }

    .form-group textarea {
        min-height: 200px;
        resize: vertical;
    }

    .form-buttons {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        margin-top: 30px;
    }

    .save-btn {
        background: #f39c12;
    }

    .cancel-btn {
        background: #e74c3c;
        color: white;
        text-decoration: none;
        padding: 12px 24px;
        border-radius: 4px;
        transition: background-color 0.3s ease;
    }

    .success {
        background: rgba(40,167,69,0.2);
        border: 1px solid rgba(40,167,69,0.4);
        color: #28a745;
        padding: 15px;
        margin: 15px 0;
        border-radius: 4px;
        text-align: center;
    }

    .error {
        background: rgba(231,76,60,0.2);
        border: 1px solid rgba(231,76,60,0.4);
        color: #e74c3c;
        padding: 15px;
        margin: 15px 0;
        border-radius: 4px;
        text-align: center;
    }
</style>';
?>