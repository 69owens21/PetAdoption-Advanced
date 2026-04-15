<?php
session_start();
require 'db_connect.php';

// Security check: Make sure they are logged in.
// (In a future update, check if $_SESSION['is_admin'] == true here!)
if (!isset($_SESSION['logged_in_user'])) {
    header("Location: index.php");
    exit();
}

$success_message = "";
$error_message = "";

// --- PROCESS FORM SUBMISSION ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Grab all the data from the HTML form
    $name = $_POST['name'];
    $breed = $_POST['breed'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $type_id = $_POST['type_id'];
    $image_url = $_POST['image_url'];

    try {
        // The SQL INSERT Query
        // Notice we default adoption_status to 'Available'
        $stmt = $pdo->prepare("
            INSERT INTO pets (name, breed, age, gender, type_id, image_url, adoption_status) 
            VALUES (:name, :breed, :age, :gender, :type_id, :image_url, 'Available')
        ");

        $stmt->execute([
            'name' => $name,
            'breed' => $breed,
            'age' => $age,
            'gender' => $gender,
            'type_id' => $type_id,
            'image_url' => $image_url
        ]);

        $success_message = "Success! $name has been added to the shelter database.";
    } catch (PDOException $e) {
        $error_message = "Error adding pet: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Add New Pet</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background-color: #f4f4f9; padding: 20px; }
        .form-box { background: white; padding: 20px; border-radius: 10px; width: 400px; margin: 0 auto; box-shadow: 0px 0px 10px gray; text-align: left; }
        label { font-weight: bold; margin-top: 10px; display: block; }
        input, select, button { margin: 5px 0 15px 0; padding: 10px; width: 100%; box-sizing: border-box; }
        button { background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
        button:hover { background-color: #218838; }
        a.back-btn { display: inline-block; margin-bottom: 20px; padding: 10px 20px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 5px; }
        .success { color: #155724; background-color: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .error { color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
    </style>
</head>
<body>

<a href="dashboard.php" class="back-btn">← Back to Dashboard</a>

<h1>Admin Panel: Add a New Pet</h1>

<div class="form-box">
    <?php if ($success_message): ?>
        <div class="success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form action="admin_add_pet.php" method="POST">
        <label>Pet Name:</label>
        <input type="text" name="name" required placeholder="e.g., Buster">

        <label>Species:</label>
        <select name="type_id" required>
            <option value="1">Cat</option>
            <option value="2">Dog</option>
        </select>

        <label>Breed:</label>
        <input type="text" name="breed" required placeholder="e.g., Golden Retriever">

        <label>Age (Years):</label>
        <input type="number" name="age" required min="0" placeholder="e.g., 3">

        <label>Gender:</label>
        <select name="gender" required>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>

        <label>Image URL:</label>
        <input type="url" name="image_url" placeholder="Paste a link to the picture" required>

        <button type="submit">Add Pet to Database</button>
    </form>
</div>

</body>
</html>