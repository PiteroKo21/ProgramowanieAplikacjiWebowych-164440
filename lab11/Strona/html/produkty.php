<?php
session_start();
include('../html/cfg.php');
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/shopS.css">
    <title>Sklep - Historia Lotów Kosmicznych</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
   
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="nav-logo">Historia Lotów Kosmicznych</a>
            <div class="nav-cart">
                <a href="koszyk.php">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count"><?php echo isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?></span>
                </a>
            </div>
        </div>
    </nav>

    <div class="shop-container">
        <aside class="filters">
            <h3>Kategorie</h3>
            <select id="category-select" onchange="filterProducts(this.value)">
                <option value="">Wszystkie kategorie</option>
                <?php
                $query = "SELECT * FROM kategorie ORDER BY nazwa";
                $result = mysqli_query($conn, $query);
                while($cat = mysqli_fetch_assoc($result)) {
                    echo "<option value='".$cat['id']."'>".$cat['nazwa']."</option>";
                }
                ?>
            </select>
        </aside>

        <main class="products">
            <div class="product-grid">
                <?php
                $query = "SELECT p.*, k.nazwa as kategoria_nazwa 
                          FROM produkty p 
                          LEFT JOIN kategorie k ON p.kategoria = k.id 
                          WHERE p.ilosc_dostepnych > 0 
                          ORDER BY p.id DESC";
                $result = mysqli_query($conn, $query);

                while ($product = mysqli_fetch_assoc($result)) {
                    $cena_brutto = $product['cena_netto'] * (1 + $product['podatek_vat']/100);
                    ?>
                    <div class="product-card" data-category="<?php echo $product['kategoria']; ?>">
                        <div class="product-image">
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($product['zdjecie']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['tytul']); ?>">
                        </div>
                        <div class="product-details">
                            <h2><?php echo htmlspecialchars($product['tytul']); ?></h2>
                            <span class="category"><?php echo htmlspecialchars($product['kategoria_nazwa']); ?></span>
                            <p class="description"><?php echo htmlspecialchars(substr($product['opis'], 0, 100)); ?>...</p>
                            <div class="price"><?php echo number_format($cena_brutto, 2); ?> PLN</div>
                            
                            <form action="koszyk.php" method="post" class="add-to-cart">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <div class="quantity-control">
                                    <button type="button" onclick="updateQuantity(this, -1)">-</button>
                                    <input type="number" name="quantity" value="1" min="1" 
                                           max="<?php echo $product['ilosc_dostepnych']; ?>">
                                    <button type="button" onclick="updateQuantity(this, 1)">+</button>
                                </div>
                                <button type="submit" name="add_to_cart" class="add-to-cart-btn">
                                    <i class="fas fa-cart-plus"></i> Dodaj do koszyka
                                </button>
                            </form>
                            
                            <?php if ($product['ilosc_dostepnych'] < 5): ?>
                                <div class="stock-warning">Ostatnie sztuki!</div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </main>
    </div>

    <footer>
        <p>&copy; 2024 Historia Lotów Kosmicznych</p>
    </footer>

    <script>
    function updateQuantity(btn, delta) {
        const input = btn.parentElement.querySelector('input');
        const newValue = parseInt(input.value) + delta;
        const min = parseInt(input.min);
        const max = parseInt(input.max);
        
        if (newValue >= min && newValue <= max) {
            input.value = newValue;
        }
    }

    function filterProducts(category) {
        const products = document.querySelectorAll('.product-card');
        
        products.forEach(product => {
            if (!category || product.dataset.category === category) {
                product.style.display = 'block';
                setTimeout(() => {
                    product.style.opacity = '1';
                    product.style.transform = 'translateY(0)';
                }, 10);
            } else {
                product.style.opacity = '0';
                product.style.transform = 'translateY(10px)';
                setTimeout(() => {
                    product.style.display = 'none';
                }, 300);
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const categorySelect = document.getElementById('category-select');
        if (categorySelect) {
            categorySelect.addEventListener('change', function() {
                filterProducts(this.value);
            });
        }
    });
    </script>
</body>
</html>
