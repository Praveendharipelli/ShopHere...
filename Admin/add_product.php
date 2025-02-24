<?php
session_start();
include("../includes/db.php");
 
  // Ensure correct database connection

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); // Redirect non-admins to login
    exit();
}

$error_message = "";
$success_message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);

    // Basic validation
    if (empty($name) || empty($price) || !is_numeric($price)) {
        $error_message = "Please enter valid product details.";
    } else {
        // Insert into database
        $stmt = $conn->prepare("INSERT INTO products (name, price) VALUES (?, ?)");
        if ($stmt->execute([$name, $price])) {
            $success_message = "Product added successfully!";
        } else {
            $error_message = "Error adding product. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
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
        .form-container {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 450px;
            color: white;
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
            font-size: 1em;
            margin-top: 10px;
        }
        .error { color: #ff4d4d; }
        .success { color: #28a745; }
        .back-link {
            text-align: center;
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
    <div class="form-container">
        <h2>Add Product</h2>

        <?php if ($error_message): ?>
            <p class="message error"><?= htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <p class="message success"><?= htmlspecialchars($success_message); ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Product Name:</label>
            <input type="text" name="name" required>

            <label>Price:</label>
            <input type="number" step="0.01" name="price" required>

            <button type="submit">Add Product</button>
        </form>

        <div class="back-link">
            <a href="admin.php">Back to Admin Dashboard</a>
        </div>
    </div>
</body>
</html>
