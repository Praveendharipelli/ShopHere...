<?php
session_start();
include '../includes/db.php';
include("header.php"); 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$total_cost = 0;

// Handle adding to cart
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    // Check if product is already in cart
    $stmt = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        $new_quantity = $existing['quantity'] + $quantity;
        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$new_quantity, $user_id, $product_id]);
    } else {
        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $product_id, 1]);
    }
}

// Handle updating quantity via AJAX
if (isset($_POST['update_quantity_ajax'])) {
    $product_id = $_POST['product_id'];
    $quantity = (int)$_POST['quantity'];

    if ($quantity > 0) {
        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$quantity, $user_id, $product_id]);
    } else {
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
    }

    $total_cost = 0;
    $stmt = $conn->prepare("SELECT cart.product_id, products.name, products.price, cart.quantity FROM cart JOIN products ON cart.product_id = products.id WHERE cart.user_id = ?");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($cart_items as $item) {
        $total_cost += floatval($item['price']) * intval($item['quantity']);
    }

    echo json_encode(['total_cost' => number_format($total_cost, 2)]);
    exit();
}

if (isset($_POST['remove_from_cart'])) {
    $product_id = $_POST['product_id'];
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
}

$stmt = $conn->prepare("SELECT cart.product_id, products.name, products.price, products.image, cart.quantity FROM cart JOIN products ON cart.product_id = products.id WHERE cart.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($cart_items as $item) {
    $total_cost += floatval($item['price']) * intval($item['quantity']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)),
                        url('../background.jpeg') no-repeat center center fixed;
            background-size: cover;
        }
        .cart-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #004e92;
            color: #fff;
        }
        td input[type='number'] {
            width: 50px;
            text-align: center;
        }
        img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
            background-color: white;
            border: 1px solid #ddd;
        }
        button {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 5px;
        }
        button:hover {
            background: #218838;
        }
        .remove-btn {
            background: #dc3545;
        }
        .remove-btn:hover {
            background: #c82333;
        }
        .total {
            text-align: right;
            font-size: 18px;
            margin-top: 20px;
            font-weight: bold;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #0056b3;
        }
    </style>
    <script>
        $(document).ready(function(){
            $(".quantity-input").on("change", function(){
                var product_id = $(this).data("product-id");
                var quantity = $(this).val();
                $.post("cart.php", { update_quantity_ajax: true, product_id: product_id, quantity: quantity }, function(response){
                    $(".total").text("Total Cost: $" + response.total_cost);
                    var row = $("input[data-product-id='" + product_id + "']").closest("tr");
                    var price = parseFloat(row.find("td:eq(2)").text().substring(1));
                    var newTotal = price * quantity;
                    row.find("td:eq(4)").text("$" + newTotal.toFixed(2));
                }, "json");
            });
        });
    </script>
</head>
<body>
    <div class="cart-container">
        <h2>Your Shopping Cart</h2>
        <table>
            <tr>
                <th>Image</th>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
            <?php foreach ($cart_items as $item): ?>
            <tr>
                <td><img src="../<?= htmlspecialchars($item['image']); ?>" alt="<?= htmlspecialchars($item['name']); ?>"></td>
                <td><?= htmlspecialchars($item['name']); ?></td>
                <td>$<?= number_format($item['price'], 2); ?></td>
                <td><input type="number" class="quantity-input" data-product-id="<?= $item['product_id']; ?>" value="<?= $item['quantity']; ?>" min="1"></td>
                <td>$<?= number_format($item['price'] * $item['quantity'], 2); ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="product_id" value="<?= $item['product_id']; ?>">
                        <button type="submit" name="remove_from_cart" class="remove-btn">Remove</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <div class="total">Total Cost: $<?= number_format($total_cost, 2); ?></div>
    </div>
    <div style="text-align: center; margin-top: 20px;">
        <a href="../index.php" class="button">Continue Shopping</a>
    </div>
</body>
</html>
