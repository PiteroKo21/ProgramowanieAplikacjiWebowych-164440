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
    <title>Kontakt </title>
</head>
<body>
<?php
function PokazKontakt() {
    $wynik = '
    <div class="contact-form">
        <h2>Formularz Kontaktowy</h2>
        <form method="post" action="' . $_SERVER['REQUEST_URI'] . '" class="contact-form-container">
            <div class="form-group">
                <label for="email">Twój adres e-mail:</label>
                <input type="email" name="email" id="email" 
                    placeholder="np. jan.kowalski@example.com" 
                    pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" 
                    required>
            </div>
            
            <div class="form-group">
                <label for="title">Temat wiadomości:</label>
                <input type="text" name="title" id="title" 
                    placeholder="Temat twojej wiadomości" 
                    minlength="3" 
                    maxlength="100" 
                    required>
            </div>
            
            <div class="form-group">
                <label for="message">Treść wiadomości:</label>
                <textarea name="message" id="message" 
                    rows="8" 
                    placeholder="Tutaj wpisz swoją wiadomość..." 
                    minlength="10" 
                    required></textarea>
            </div>
            
            <div class="form-buttons">
                <button type="submit" name="wyslij" class="submit-btn">
                    <i class="fas fa-paper-plane"></i> Wyślij wiadomość
                </button>
            </div>
            
            <div class="form-info">
                <p>* Wszystkie pola są wymagane</p>
                <p>Odpowiedź otrzymasz na podany adres e-mail</p>
            </div>
        </form>
    </div>';
    
    return $wynik;
}

function WyslijMailKontakt($odbiorca) {
    // Sprawdzenie wymaganych pól
    if (empty($_POST['email']) || empty($_POST['title']) || empty($_POST['message'])) {
        return '<p class="error">Wszystkie pola formularza są wymagane!</p>';
    }
    
    // Walidacja adresu email
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return '<p class="error">Podany adres e-mail jest nieprawidłowy!</p>';
    }
    
    // Przygotowanie danych
    $tytul = htmlspecialchars($_POST['title']);
    $tresc = htmlspecialchars($_POST['message']);
    $data = date('Y-m-d H:i:s');
    
    // Konfiguracja nagłówków
    $headers = array(
        'From' => $email,
        'Reply-To' => $email,
        'X-Mailer' => 'PHP/' . phpversion(),
        'MIME-Version' => '1.0',
        'Content-Type' => 'text/html; charset=UTF-8'
    );
    
    // Przygotowanie treści HTML
    $wiadomosc = "
    <!DOCTYPE html>
    <html lang='pl'>
    <head>
        <meta charset='UTF-8'>
        <title>$tytul</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .header { background: #f8f9fa; padding: 20px; border-radius: 5px; }
            .content { padding: 20px; }
            .footer { font-size: 12px; color: #666; border-top: 1px solid #ddd; padding-top: 10px; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h2>Nowa wiadomość z formularza kontaktowego</h2>
            <p><strong>Data wysłania:</strong> $data</p>
        </div>
        
        <div class='content'>
            <p><strong>Od:</strong> $email</p>
            <p><strong>Temat:</strong> $tytul</p>
            <p><strong>Treść wiadomości:</strong></p>
            <p>" . nl2br($tresc) . "</p>
        </div>
        
        <div class='footer'>
            <p>Ta wiadomość została wygenerowana automatycznie przez formularz kontaktowy.</p>
            <p>IP nadawcy: " . $_SERVER['REMOTE_ADDR'] . "</p>
        </div>
    </body>
    </html>";
    
    // Próba wysłania emaila
    try {
        $header_str = '';
        foreach($headers as $key => $value) {
            $header_str .= "$key: $value\r\n";
        }
        
        if (mail($odbiorca, $tytul, $wiadomosc, $header_str)) {
            // Logowanie udanej próby wysłania
            $log_message = date('Y-m-d H:i:s') . " - Email wysłany od: $email, temat: $tytul\n";
            error_log($log_message, 3, "contact_form.log");
            
            return '<p class="success">Wiadomość została wysłana pomyślnie! Dziękujemy za kontakt.</p>';
        } else {
            throw new Exception("Błąd podczas wysyłania wiadomości");
        }
    } catch (Exception $e) {
        // Logowanie błędu
        error_log(date('Y-m-d H:i:s') . " - Błąd wysyłania: " . $e->getMessage() . "\n", 3, "mail_errors.log");
        return '<p class="error">Wystąpił błąd podczas wysyłania wiadomości. Prosimy spróbować później.</p>';
    }
}

function PrzypomnijHaslo() {

    global $login, $pass;
    
    if (empty($_POST['email'])) {
        return '<p class="error">Proszę podać adres e-mail!</p>';
    }
    
    $email = $_POST['email'];
    $tytul = "Przypomnienie hasła - Panel Administracyjny";
    
    // Używamy tej samej struktury nagłówków co w WyslijMailKontakt()
    $headers = "From: admin@example.com\r\n";
    $headers .= "Reply-To: admin@example.com\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    

    $tresc = "
    <html>
    <head>
        <title>Przypomnienie hasła</title>
        <style>
            .warning {
                color: #721c24;
                background: #f8d7da;
                padding: 10px;
                margin: 10px 0;
                border: 1px solid #f5c6cb;
                border-radius: 4px;
            }
            .credentials {
                background: #e9ecef;
                padding: 15px;
                margin: 10px 0;
                border-radius: 4px;
            }
        </style>
    </head>
    <body>
        <h2>Przypomnienie danych logowania</h2>
        
        <div class='warning'>
            <strong>UWAGA! Wiadomość zawiera poufne dane dostępowe!</strong> 
            <p>Ze względów bezpieczeństwa zalecamy:</p>
            <ul>
                <li>Natychmiastową zmianę hasła po zalogowaniu</li>
                <li>Nieudostępnianie tych danych osobom trzecim</li>
                <li>Usunięcie tej wiadomości po zapoznaniu się z treścią</li>
            </ul>
        </div>

        <div class='credentials'>
            <p><strong>Twoje dane logowania do panelu administracyjnego:</strong></p>
            <p>Login: $login</p>
            <p>Hasło: $pass</p>
        </div>

        <p>Ta wiadomość została wygenerowana automatycznie. Nie odpowiadaj na nią.</p>
        
        <hr>
        <p style='font-size: 12px; color: #666;'>
            Jeśli nie prosiłeś o przypomnienie hasła, zignoruj tę wiadomość i natychmiast 
            skontaktuj się z administratorem systemu pod adresem: admin@example.com
        </p>
    </body>
    </html>";
    
    // Używamy funkcji mail() tak jak w WyslijMailKontakt()
    if (mail($email, $tytul, $tresc, $headers)) {
        // Logujemy próbę przypomnienia hasła dla celów bezpieczeństwa
        $log_message = date('Y-m-d H:i:s') . " - Próba przypomnienia hasła dla adresu: " . $email . "\n";
        error_log($log_message, 3, "password_reset_attempts.log");
        
        return '
        <div class="success">
            <p>Dane logowania zostały wysłane na podany adres e-mail.</p>
            <p><strong>UWAGA:</strong> Ze względów bezpieczeństwa zalecamy natychmiastową zmianę hasła po zalogowaniu.</p>
        </div>';
    } else {
        return '<p class="error">Wystąpił błąd podczas wysyłania e-maila. Spróbuj ponownie później.</p>';
    }
}

// Dodaj formularz przypomnienia hasła
function PokazFormularzPrzypomnieniaHasla() {
    return '
    <div class="contact-form">
        <h2>Przypomnienie hasła</h2>
        <form method="post" action="' . $_SERVER['REQUEST_URI'] . '" class="contact-form-container">
            <div class="form-group">
                <label for="email">Podaj swój adres e-mail:</label>
                <input type="email" name="email" id="email" 
                    placeholder="np. jan.kowalski@example.com" 
                    pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" 
                    required>
            </div>
            
            <div class="form-buttons">
                <button type="submit" name="przypomnij" class="submit-btn">
                    <i class="fas fa-key"></i> Przypomnij hasło
                </button>
            </div>
            
            <div class="form-info">
                <p>* Na podany adres e-mail zostaną wysłane dane logowania</p>
                <p>* Ze względów bezpieczeństwa zalecamy zmianę hasła po zalogowaniu</p>
            </div>
        </form>
    </div>';
}

// Modyfikacja obsługi formularzy
if (isset($_POST['wyslij'])) {
    echo WyslijMailKontakt('admin@example.com');
} elseif (isset($_POST['przypomnij'])) {
    echo PrzypomnijHaslo();
} elseif (isset($_GET['action']) && $_GET['action'] == 'przypomnij') {
    echo PokazFormularzPrzypomnieniaHasla();
} else {
    echo PokazKontakt();
}
?>
</body>
</html> 