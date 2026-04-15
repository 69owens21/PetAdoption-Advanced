<?php
session_start();
require 'db_connect.php';

// Security check: Make sure they are logged in
if (!isset($_SESSION['logged_in_user'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['logged_in_user'];
$success_message = "";
$error_message = "";

// --- PROCESS FORM SUBMISSIONS ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];

    try {
        // 1. UPDATE PASSWORD
        if ($action == 'update_password') {
            $old_password = $_POST['old_password'];
            $new_password = $_POST['new_password'];

            // Check if old password matches
            $stmt = $pdo->prepare("SELECT password FROM users WHERE username = :username AND password = :old_password");
            $stmt->execute(['username' => $username, 'old_password' => $old_password]);

            if ($stmt->fetch()) {
                // Password matches, update it!
                $update = $pdo->prepare("UPDATE users SET password = :new_password WHERE username = :username");
                $update->execute(['new_password' => $new_password, 'username' => $username]);
                $success_message = "Password updated successfully!";
            } else {
                $error_message = "Incorrect current password.";
            }
        }

        // 2. UPDATE EMAIL
        elseif ($action == 'update_email') {
            $new_email = $_POST['new_email'];
            $update = $pdo->prepare("UPDATE users SET email = :new_email WHERE username = :username");
            $update->execute(['new_email' => $new_email, 'username' => $username]);
            $success_message = "Email updated successfully!";
        }

        // 3. UPDATE PHONE NUMBER
        elseif ($action == 'update_phone') {
            $new_phone = $_POST['new_phone'];
            $update = $pdo->prepare("UPDATE users SET phone = :new_phone WHERE username = :username");
            $update->execute(['new_phone' => $new_phone, 'username' => $username]);
            $success_message = "Phone number updated successfully!";
        }

        // 4. DELETE ACCOUNT
        elseif ($action == 'delete_account') {
            $confirm_password = $_POST['confirm_password'];

            // Verify password before deleting
            $stmt = $pdo->prepare("SELECT password FROM users WHERE username = :username AND password = :password");
            $stmt->execute(['username' => $username, 'password' => $confirm_password]);

            if ($stmt->fetch()) {
                // They entered the right password. Delete the account!
                $delete = $pdo->prepare("DELETE FROM users WHERE username = :username");
                $delete->execute(['username' => $username]);

                // Destroy their session and kick them to the login screen
                session_destroy();
                header("Location: index.php");
                exit();
            } else {
                $error_message = "Incorrect password. Account deletion canceled.";
            }
        }

    } catch (PDOException $e) {
        // If they enter an email or phone that already belongs to someone else,
        // PostgreSQL's UNIQUE constraint will throw an error. We catch it here!
        if ($e->getCode() == 23505) { // 23505 is the specific Postgres code for a unique violation
            $error_message = "Error: That email or phone number is already taken.";
        } else {
            $error_message = "An error occurred: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account Settings</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background-color: #f4f4f9; padding: 20px; }
        .settings-container { display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; max-width: 800px; margin: 0 auto; }
        .form-box { background: white; padding: 20px; border-radius: 10px; width: 300px; box-shadow: 0px 0px 10px gray; text-align: left; }
        .danger-zone { border: 2px solid #dc3545; }
        input, button { margin: 10px 0; padding: 10px; width: 90%; box-sizing: border-box; }
        button { background-color: #007BFF; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        button.danger-btn { background-color: #dc3545; }
        button.danger-btn:hover { background-color: #c82333; }
        a.back-btn { display: inline-block; margin-bottom: 20px; padding: 10px 20px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 5px; }
        .success { color: #155724; background-color: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin-bottom: 20px; width: 80%; margin: 0 auto 20px auto; }
        .error { color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin-bottom: 20px; width: 80%; margin: 0 auto 20px auto; }
    </style>
</head>
<body>

<a href="dashboard.php" class="back-btn">← Back to Dashboard</a>

<h1>Account Settings</h1>

<?php if ($success_message): ?>
    <div class="success"><?php echo $success_message; ?></div>
<?php endif; ?>

<?php if ($error_message): ?>
    <div class="error"><?php echo $error_message; ?></div>
<?php endif; ?>

<div class="settings-container">

    <div class="form-box">
        <h3>Change Password</h3>
        <form action="account_settings.php" method="POST">
            <input type="hidden" name="action" value="update_password">
            <input type="password" name="old_password" placeholder="Current Password" required>
            <input type="password" name="new_password" placeholder="New Password" required>
            <button type="submit">Update Password</button>
        </form>
    </div>

    <div class="form-box">
        <h3>Update Email</h3>
        <form action="account_settings.php" method="POST">
            <input type="hidden" name="action" value="update_email">
            <input type="email" name="new_email" placeholder="New Email Address" required>
            <button type="submit">Update Email</button>
        </form>
    </div>

    <div class="form-box">
        <h3>Update Phone Number</h3>
        <form action="account_settings.php" method="POST">
            <input type="hidden" name="action" value="update_phone">
            <input type="text" name="new_phone" placeholder="New Phone Number" required>
            <button type="submit">Update Phone</button>
        </form>
    </div>

    <div class="form-box danger-zone">
        <h3 style="color: #dc3545;">Delete Account</h3>
        <p style="font-size: 12px; color: gray;">This action cannot be undone.</p>
        <form action="account_settings.php" method="POST">
            <input type="hidden" name="action" value="delete_account">
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit" class="danger-btn">Delete My Account</button>
        </form>
    </div>

</div>

</body>
</html>