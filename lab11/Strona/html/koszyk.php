<?php
session_start();
include('../html/cfg.php');

// Inicjalizacja koszyka
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Dodawanie produktu do koszyka
if (isset($_POST['add_to_cart'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    // Sprawdzenie, czy produkt już jest w koszyku
    if (array_key_exists($product_id, $_SESSION['cart'])) {
        $_SESSION['cart'][$product_id] += $quantity; // Zwiększ ilość
    } else {
        $_SESSION['cart'][$product_id] = $quantity; // Dodaj nowy produkt
    }
}

// Usuwanie produktu z koszyka
if (isset($_POST['remove_from_cart'])) {
    $product_id = (int)$_POST['product_id'];
    unset($_SESSION['cart'][$product_id]); // Usunięcie produktu z koszyka
}

// Wyświetlanie koszyka
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <title>Koszyk</title>
</head>
<body class="body">
    <header class="subpage-header">
        <h1>Koszyk</h1>
    </header>

    <section class="content">
        <div class="cart">
            <h2>Produkty w koszyku</h2>
            <?php
            if (empty($_SESSION['cart'])) {
                echo '<p>Koszyk jest pusty.</p>';
            } else {
                echo '<ul>';
                $total = 0;
                foreach ($_SESSION['cart'] as $product_id => $quantity) {
                    $query = "SELECT * FROM produkty WHERE id = $product_id";
                    $result = mysqli_query($conn, $query);
                    if ($row = mysqli_fetch_assoc($result)) {
                        $subtotal = $row['cena_netto'] * $quantity;
                        $total += $subtotal;
                        echo '<li>
                            <div>
                                <span>' . htmlspecialchars($row['tytul']) . '</span>
                                <span> - ' . number_format($row['cena_netto'], 2) . ' PLN × ' . $quantity . ' = ' . number_format($subtotal, 2) . ' PLN</span>
                            </div>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="product_id" value="' . $product_id . '">
                                <button type="submit" name="remove_from_cart" class="remove-btn">Usuń</button>
                            </form>
                        </li>';
                    }
                }
                echo '</ul>';
                echo '<p class="total">Suma: ' . number_format($total, 2) . ' PLN</p>';
            }
            ?>
            <a href="produkty.php" class="continue-shopping">Kontynuuj zakupy</a>
        </div>
    </section>

    <footer class="footer">
        <p>© 2024 Historia Lotów Kosmicznych. Wszystkie prawa zastrzeżone.</p>
    </footer>
</body>
</html> 