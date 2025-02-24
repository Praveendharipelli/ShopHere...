<?php
session_start();
include("../includes/db.php");
include("header.php"); 


$timeout_duration = 900;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: ../login.php");
    exit();
}

$_SESSION['last_activity'] = time();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM products");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)),
                        url('../background.jpeg');`
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: #f4f4f4;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 85%;
            margin: 30px auto;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
            border-radius: 12px;
        }

        h2, h3 {
    color: #1a1a1a;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
}

        .button {
            background: linear-gradient(135deg, #28a745, #218838);
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: bold;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .button:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .go-home {
            background: linear-gradient(135deg, #007bff, #0056b3);
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ccc;
        }

        th {
            background-color: rgba(9, 9, 23, 0.9);
        }

        td {
            background-color: rgba(255, 255, 255, 0.8);
            color: #333;
        }

        .actions a {
            background-color: #007bff;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            margin-right: 10px;
        }

        .actions a:hover {
            background-color: #0056b3;
        }

        .delete-button {
            background-color: #dc3545;
        }

        .delete-button:hover {
            background-color: #c82333;
        }

        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            display: none;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .modal-buttons .button {
            background-color: #e74c3c;
        }

        .modal-buttons .button.cancel {
            background-color: #95a5a6;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Admin Dashboard</h2>
    <a href="../index.php" class="button go-home">Go to Homepage</a>
    <a href="add_product.php" class="button">Add New Product</a>
    <h3>Product List</h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product Name</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php $index = 1; foreach ($products as $product): ?>
        <tr>
            <td><?= $index++; ?></td>
            <td><?= htmlspecialchars($product['name']); ?></td>
            <td><?= $product['price']; ?></td>
            <td class="actions">
                <a href="edit_product.php?id=<?= $product['id']; ?>" class="button">Edit</a>
                <a href="#" onclick="openModal(<?= $product['id']; ?>)" class="button delete-button">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="confirmationModal" class="modal">
    <div class="modal-content">
        <h2>Are you sure you want to delete this product?</h2>
        <div class="modal-buttons">
            <button onclick="deleteProduct()" class="button">Yes, Delete</button>
            <button onclick="closeModal()" class="button cancel">Cancel</button>
        </div>
    </div>
</div>

<script>
    let productIdToDelete = null;
    function openModal(productId) {
        productIdToDelete = productId;
        document.getElementById('confirmationModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('confirmationModal').style.display = 'none';
    }

    function deleteProduct() {
        window.location.href = "delete_product.php?id=" + productIdToDelete;
    }

    let timeout;
    function resetSessionTimeout() {
        clearTimeout(timeout);
        timeout = setTimeout(function() {
            window.location.href = 'logout.php';
        }, 900000);
    }

    window.onload = resetSessionTimeout;
    document.onmousemove = resetSessionTimeout;
    document.onkeydown = resetSessionTimeout;
    document.onclick = resetSessionTimeout;
</script>

</body>
</html>
