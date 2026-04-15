<?php
// 1. Start a "Session" to remember who is logged in
session_start();

// 2. Bring in our database connection
require 'db_connect.php';

// 3. Check if the form was actually submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Grab the data from the HTML form
    $form_username = $_POST['username'];
    $form_password = $_POST['password'];

    // 4. The SQL Query
    // We use "prepared statements" (:username) to prevent hackers from injecting bad SQL
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username AND password = :password");

    // Execute the query by plugging in the form data
    $stmt->execute([
        'username' => $form_username,
        'password' => $form_password
    ]);

    // Fetch the user from the database
    $user = $stmt->fetch();

    // 5. The Logic Check
    if ($user) {
        // Success! Save their username in the session and send them to the dashboard
        $_SESSION['logged_in_user'] = $user['username'];
        header("Location: dashboard.php");
        exit();
    } else {
        // Failure! Show an error
        echo "<h3>Invalid username or password.</h3>";
        echo "<a href='index.php'>Click here to try again</a>";
    }
}
?>