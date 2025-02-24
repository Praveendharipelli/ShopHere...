<?php
session_start();
include("../includes/db.php");
   // Database connection

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); // Redirect to login if not admin
    exit();
}

// Check if product ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: admin.php"); // Redirect if no product ID is provided
    exit();
}

$product_id = $_GET['id'];
$error_message = "";
$success_message = "";

// Fetch product details
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: admin.php"); // Redirect if product doesn't exist
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);

    // Validate input
    if (empty($name) || empty($price) || !is_numeric($price)) {
        $error_message = "Please enter valid product details.";
    } else {
        // Update product details
        $stmt = $conn->prepare("UPDATE products SET name = ?, price = ? WHERE id = ?");
        if ($stmt->execute([$name, $price, $product_id])) {
            $success_message = "Product updated successfully!";
            // Refresh product data
            $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $error_message = "Error updating product. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)),
                        url('../background.jpeg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            width: 100%;
            max-width: 450px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
            color: white;
        }
        .logo {
            display: block;
            margin: 0 auto 20px;
            width: 100px;
            height: auto;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            font-size: 1.1em;
            margin-bottom: 5px;
            display: block;
        }
        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: none;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            outline: none;
            transition: background 0.3s ease;
        }
        input:focus {
            background: rgba(255, 255, 255, 0.3);
        }
        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #4CAF50, #2E7D32);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 1.1em;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        button:hover {
            background: linear-gradient(135deg, #388E3C, #1B5E20);
        }
        .message {
            text-align: center;
            font-size: 1.1em;
            margin-top: 10px;
        }
        .error {
            color: #ff4d4d;
        }
        .success {
            color: #28a745;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: #00bcd4;
            text-decoration: none;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <img src="../logo.png" alt="Logo" class="logo">
    <h2>Edit Product</h2>

    <?php if ($error_message): ?>
        <p class="message error"><?= htmlspecialchars($error_message); ?></p>
    <?php endif; ?>

    <?php if ($success_message): ?>
        <p class="message success"><?= htmlspecialchars($success_message); ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Product Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($product['name']); ?>" required>

        <label>Price:</label>
        <input type="number" step="100" name="price" value="<?= $product['price']; ?>" required>

        <button type="submit">Update Product</button>
    </form>

    <div class="back-link">
        <a href="admin.php">Back to Admin Dashboard</a>
    </div>
</div>

</body>
</html>
