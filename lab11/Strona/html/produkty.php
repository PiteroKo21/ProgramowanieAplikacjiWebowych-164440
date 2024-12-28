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
    <title>Produkty</title>
</head>
<body class="body">
    <header class="subpage-header">
        <h1>Produkty</h1>
        <div class="header-cart">
            <a href="koszyk.php" class="cart-link">Koszyk (<?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>)</a>
        </div>
    </header>

    <section class="content">
        <div class="product-list">
            <?php
            // Zapytanie do bazy danych o produkty
            $query = "SELECT * FROM produkty WHERE ilosc_dostepnych > 0";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<div class="product">';
                    echo '<h2>' . htmlspecialchars($row['tytul']) . '</h2>';
                    echo '<p>' . htmlspecialchars($row['opis']) . '</p>';
                    echo '<p>Cena: ' . htmlspecialchars($row['cena_netto']) . ' PLN</p>';
                    
                    // Sprawdzenie, czy zdjęcie jest przechowywane jako BLOB
                    if ($row['zdjecie']) {
                        echo '<img src="data:image/jpeg;base64,' . base64_encode($row['zdjecie']) . '" alt="' . htmlspecialchars($row['tytul']) . '" class="product-image">';
                    } else {
                        // Jeśli zdjęcie jest przechowywane jako ścieżka
                        echo '<img src="../img/' . htmlspecialchars($row['zdjecie']) . '" alt="' . htmlspecialchars($row['tytul']) . '" class="product-image">';
                    }

                    echo '<form method="post" action="koszyk.php">
                            <input type="hidden" name="product_id" value="' . $row['id'] . '">
                            <label for="quantity">Ilość:</label>
                            <input type="number" name="quantity" value="1" min="1" required>
                            <input type="submit" name="add_to_cart" value="Dodaj do koszyka">
                          </form>';
                    echo '</div>';
                }
            } else {
                echo '<p>Brak dostępnych produktów.</p>';
            }
            ?>
        </div>
    </section>

    <footer class="footer">
        <p>© 2024 Historia Lotów Kosmicznych. Wszystkie prawa zastrzeżone.</p>
    </footer>
</body>
</html>
