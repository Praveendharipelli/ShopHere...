<?php
session_start();
include("../includes/db.php"); 
  // Database connection

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); // Redirect to login if not admin
    exit();
}

// Check if product ID is provided in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: admin.php"); // Redirect if no product ID
    exit();
}

$product_id = $_GET['id'];

// Delete the product
$stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
if ($stmt->execute([$product_id])) {
    $_SESSION['message'] = "Product deleted successfully!";
} else {
    $_SESSION['error'] = "Error deleting product. Try again.";
}

// Redirect back to admin dashboard
header("Location: admin.php");
exit();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Product</title>
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
        .message-container {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 450px;
            color: white;
            text-align: center;
        }
        .logo {
            display: block;
            margin: 0 auto 20px;
            width: 100px;
            height: auto;
        }
        h2 {
            margin-bottom: 20px;
        }
        .message {
            font-size: 1.2em;
            margin-bottom: 20px;
        }
        .message.success {
            color: #28a745;
        }
        .message.error {
            color: #ff4d4d;
        }
        .back-link {
            margin-top: 15px;
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
    <div class="message-container">
        <img src="../logo.png" alt="Logo" class="logo">
        <h2>Product Deletion</h2>
        
        <?php if (isset($_SESSION['message'])): ?>
            <p class="message success"><?= htmlspecialchars($_SESSION['message']); ?></p>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <p class="message error"><?= htmlspecialchars($_SESSION['error']); ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="back-link">
            <a href="admin.php">Back to Admin Dashboard</a>
        </div>
    </div>
</body>
</html>
