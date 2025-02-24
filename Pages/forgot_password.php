<?php
include('../includes/db.php');
  // Database connection
session_start();

if (!isset($_SESSION['verification_code'])) {
    $_SESSION['verification_code'] = rand(100000, 999999);
}

if (isset($_POST['submit_email'])) {
    $email = $_POST['email'];

    // Check if the email exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $success_message = "Verification code sent to your email. (Simulated Code: " . $_SESSION['verification_code'] . ")";
    } else {
        $error_message = "No account found with this email.";
    }
}

if (isset($_POST['reset_password'])) {
    $entered_code = $_POST['verification_code'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];

    if ($_SESSION['verification_code'] == $entered_code) {
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$new_password, $email]);

        unset($_SESSION['verification_code']);
        
        // Redirect to login page after successful reset
        header("Location: login.php?message=Password reset successfully! Please log in.");
        exit();
    } else {
        $error_message = "Invalid verification code.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
                        url('../background.jpeg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .reset-container {
            background-color: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 450px;
            color: white;
            animation: slide-in 0.5s ease;
        }

        @keyframes slide-in {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo {
            display: block;
            margin: 0 auto 20px;
            width: 100px;  /* Adjust size of your logo */
            height: auto;
            /* Glowing effect */
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.8), 0 0 30px rgba(255, 255, 255, 0.6), 0 0 40px rgba(0, 255, 255, 0.5);
            transition: box-shadow 0.3s ease-in-out;
        }

        .logo:hover {
            /* Hover glowing effect */
            box-shadow: 0 0 30px rgba(255, 255, 255, 0.9), 0 0 40px rgba(0, 255, 255, 0.7), 0 0 50px rgba(255, 0, 255, 0.6);
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
        }

        input:focus {
            background: rgba(255, 255, 255, 0.3);
            box-shadow: 0 0 8px rgba(255, 255, 255, 0.4);
        }

        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #4CAF50, #2E7D32);
            color: white;
            font-size: 1.1em;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background: linear-gradient(135deg, #388E3C, #1B5E20);
        }

        .success-message {
            color: #28a745;
            text-align: center;
            margin-top: 10px;
        }

        .error-message {
            color: #e74c3c;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <!-- Logo Section -->
        <img src="../logo.png" alt="Logo" class="logo">
        
        <h2>Forgot Password</h2>
        <form method="POST">
            <label>Email:</label>
            <input type="email" name="email" required>

            <?php if (isset($success_message)): ?>
                <label>Enter Verification Code:</label>
                <input type="text" name="verification_code" required>

                <label>New Password:</label>
                <input type="password" name="new_password" required>

                <button type="submit" name="reset_password">Reset Password</button>
            <?php else: ?>
                <button type="submit" name="submit_email">Send Verification Code</button>
            <?php endif; ?>
        </form>

        <?php if (isset($success_message)): ?>
            <p class="success-message"><?= htmlspecialchars($success_message); ?></p>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <p class="error-message"><?= htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
