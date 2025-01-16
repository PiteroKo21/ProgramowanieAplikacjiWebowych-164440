<?php
session_start();
include('../html/cfg.php');
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koszyk - Historia Lotów Kosmicznych</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="nav-logo">Historia Lotów Kosmicznych</a>
            <div class="nav-cart">
                <a href="produkty.php">
                    <i class="fas fa-store"></i>
                    <span>Wróć do sklepu</span>
                </a>
            </div>
        </div>
    </nav>

    <div class="cart-page">
        <div class="cart-container">
            <h1 class="cart-title"><i class="fas fa-shopping-cart"></i> Twój Koszyk</h1>

            <?php if (empty($_SESSION['cart'])): ?>
                <div class="empty-cart">
                    <i class="fas fa-shopping-basket"></i>
                    <p>Twój koszyk jest pusty</p>
                    <a href="produkty.php" class="continue-shopping">
                        <i class="fas fa-arrow-left"></i> Przejdź do sklepu
                    </a>
                </div>
            <?php else: ?>
                <div class="cart-content">
                    <div class="cart-items">
                        <?php
                        $total = 0;
                        foreach ($_SESSION['cart'] as $product_id => $quantity):
                            $query = "SELECT * FROM produkty WHERE id = $product_id";
                            $result = mysqli_query($conn, $query);
                            if ($row = mysqli_fetch_assoc($result)):
                                $subtotal = $row['cena_netto'] * (1 + $row['podatek_vat']/100) * $quantity;
                                $total += $subtotal;
                        ?>
                            <div class="cart-item">
                                <div class="item-image">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($row['zdjecie']); ?>" 
                                         alt="<?php echo htmlspecialchars($row['tytul']); ?>">
                                </div>
                                <div class="item-details">
                                    <h3><?php echo htmlspecialchars($row['tytul']); ?></h3>
                                    <p class="item-price"><?php echo number_format($row['cena_netto'] * (1 + $row['podatek_vat']/100), 2); ?> PLN</p>
                                </div>
                                <div class="item-quantity">
                                    <form method="post" class="quantity-form">
                                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                        <button type="button" onclick="updateCartQuantity(this, -1)" class="quantity-btn">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" name="quantity" value="<?php echo $quantity; ?>" 
                                               min="1" max="99" class="quantity-input" onchange="this.form.submit()">
                                        <button type="button" onclick="updateCartQuantity(this, 1)" class="quantity-btn">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </form>
                                </div>
                                <div class="item-subtotal">
                                    <p><?php echo number_format($subtotal, 2); ?> PLN</p>
                                </div>
                                <div class="item-remove">
                                    <form method="post">
                                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                        <button type="submit" name="remove_from_cart" class="remove-btn">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>

                    <div class="cart-summary">
                        <h2>Podsumowanie zamówienia</h2>
                        <div class="summary-details">
                            <div class="summary-row">
                                <span>Suma częściowa:</span>
                                <span><?php echo number_format($total, 2); ?> PLN</span>
                            </div>
                            <div class="summary-row">
                                <span>Dostawa:</span>
                                <span>0.00 PLN</span>
                            </div>
                            <div class="summary-row total">
                                <span>Razem:</span>
                                <span><?php echo number_format($total, 2); ?> PLN</span>
                            </div>
                        </div>
                        <div class="cart-buttons">
                            <a href="produkty.php" class="continue-shopping">
                                <i class="fas fa-arrow-left"></i> Kontynuuj zakupy
                            </a>
                            <button class="checkout-btn">
                                <i class="fas fa-lock"></i> Przejdź do kasy
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function updateCartQuantity(btn, delta) {
        const input = btn.parentElement.querySelector('input[type="number"]');
        const newValue = parseInt(input.value) + delta;
        if (newValue >= parseInt(input.min) && newValue <= parseInt(input.max)) {
            input.value = newValue;
            input.form.submit();
        }
    }
    </script>
</body>
</html> 