<?php
session_start(); // <--- This wakes up PHP's memory!

// If they aren't logged in, kick them back to the login page
if (!isset($_SESSION['logged_in_user'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin-top: 50px; background-color: #f4f4f9; }
        .menu-box { background: white; padding: 20px; border-radius: 10px; width: 400px; margin: 0 auto; box-shadow: 0px 0px 10px gray; }
        a.button { display: block; margin: 10px 0; padding: 10px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px; }
        a.button:hover { background-color: #218838; }
        a.button.logout { background-color: #dc3545; }
        a.button.logout:hover { background-color: #c82333; }
    </style>
</head>
<body>

<h1>Welcome back, <?php echo htmlspecialchars($_SESSION['logged_in_user']); ?>!</h1>
<p>What would you like to do today?</p>

<div class="menu-box">
    <a href="view_pets.php" class="button"> View Available Pets</a>
    <a href="search_pets.php" class="button"> Search Pets by Breed</a>
    <a href="adopt_pet.php" class="button"> Adopt a Pet</a>
    <a href="account_settings.php" class="button"> Account Settings</a>
    <a href="view_history.php" class="button"> View Your Adoption History</a>
    <a href="view_stats.php" class="button"> View Shelter Statistics</a>
    <a href="logout.php" class="button logout"> Log Out</a>
</div>

</body>
</html>