<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    // Uncomment the following line to redirect to login
    // header("Location: pages/login.php");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: pages/login.php");
    exit();
}

include 'includes/db.php';

if (!$conn) {
    die("Database connection error");
}

$stmt = $conn->prepare("SELECT * FROM products ORDER BY name ASC");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Store</title>
 
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #eef1f5;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        header {
            background-color: #2c3e50;
            color: white;
            padding: 6px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        nav {
            display: flex;
            gap: 16px;
            margin-right: 20px;
        }
        nav button {
            color: white;
            background-color: #34495e;
            border: none;
            cursor: pointer;
            font-size: 16px;
            padding: 12px 20px;
            border-radius: 4px;
            text-transform: uppercase;
        }
        nav button:hover {
            background-color: #1abc9c;
        }
        .logout-button {
            background-color: #e74c3c;
        }
        .logout-button:hover {
            background-color: #c0392b;
        }
        .main-container {
            width: 90%;
            margin: 80px auto 30px;
            flex: 1;
        }
        .product-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .product {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 250px;
            transition: transform 0.2s ease-in-out;
        }
        .product:hover {
            transform: scale(1.03);
        }
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }
        .price {
            margin: 10px 0;
            font-weight: bold;
            color: #27ae60;
        }
        .add-to-cart-button {
            background-color: #27ae60;
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            transition: box-shadow 0.3s ease-in-out, background-color 0.3s;
            font-size: 16px;
        }
        .add-to-cart-button:hover {
            background-color: #1e8449;
            box-shadow: 0 0 10px #27ae60;
        }
        footer {
            background-color: #2c3e50;
            color: white;
            padding: 2px;
            text-align: center;
            margin-top: auto;
        }
    </style>
</head>
<body>
    <header>
        <h1>Our Store</h1>
        <nav>
            <button onclick="location.href='pages/login.php'">LOGIN</button>
            <button onclick="location.href='pages/register.php'">REGISTER</button>
            <button onclick="location.href='pages/cart.php'">CART</button>
            <form method="POST" style="display: inline;">
                <button type="submit" name="logout" class="logout-button">LOGOUT</button>
            </form>
        </nav>
    </header>
    <div class="main-container">
        <main>
            <h2>Browse Products</h2>
            <div class="product-list">
                <?php if (empty($products)) : ?>
                    <p>No products available.</p>
                <?php else : ?>
                    <?php foreach ($products as $product) : ?>
                        <div class="product">
                            <?php 
                                $imgSrc = !empty($product['image']) ? htmlspecialchars($product['image']) : 'images/default-product.jpg';
                            ?>
                            <img src="<?= $imgSrc; ?>" alt="<?= htmlspecialchars($product['name']); ?>" class="product-image">
                            <h3><?= htmlspecialchars($product['name']); ?></h3>
                            <p class="price">$<?= number_format($product['price'], 2); ?></p>
                            <p><?= htmlspecialchars($product['description']); ?></p>
                            <form method="POST" action="pages/cart.php">
                                <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" name="add_to_cart" class="add-to-cart-button">ADD TO CART</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
    <footer>
        <p>&copy; <?= date('Y'); ?> Our Online Store</p>
        <p>All rights reserved.</p>
    </footer>
</body>
</html>
