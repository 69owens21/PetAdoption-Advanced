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

// --- PART 1: PROCESS THE ADOPTION IF THE FORM WAS SUBMITTED ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pet_id'])) {
    $pet_id = $_POST['pet_id'];

    try {
        // THE SQL TRANSACTION STARTS HERE!
        // This ensures both queries succeed together, or neither of them do.
        $pdo->beginTransaction();

        // 1. Update the pet's status to 'Adopted'
        $update_stmt = $pdo->prepare("UPDATE pets SET adoption_status = 'Adopted' WHERE pet_id = :pet_id");
        $update_stmt->execute(['pet_id' => $pet_id]);

        // 2. Insert the record into the adoptions table
        $insert_stmt = $pdo->prepare("INSERT INTO adoptions (username, pet_id) VALUES (:username, :pet_id)");
        $insert_stmt->execute([
            'username' => $username,
            'pet_id' => $pet_id
        ]);

        // If both worked, COMMIT the changes to the database!
        $pdo->commit();

        $success_message = "Congratulations! You have successfully adopted your new best friend! We will contact you with the next steps.";

    } catch (Exception $e) {
        // If anything fails, ROLLBACK the changes so the database isn't corrupted
        $pdo->rollBack();
        $error_message = "Something went wrong with the adoption. Please try again.";
    }
}

// --- PART 2: FETCH AVAILABLE PETS FOR THE DROPDOWN MENU ---
$stmt = $pdo->query("
    SELECT pets.pet_id, pets.name, pets.breed, pet_types.name AS species 
    FROM pets 
    INNER JOIN pet_types ON pets.type_id = pet_types.type_id 
    WHERE adoption_status = 'Available'
    ORDER BY species, pets.name
");
$available_pets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Adopt a Pet</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background-color: #f4f4f9; padding: 20px; }
        .form-box { background: white; padding: 20px; border-radius: 10px; width: 400px; margin: 0 auto; box-shadow: 0px 0px 10px gray; }
        select, button { margin: 15px 0; padding: 10px; width: 90%; font-size: 16px; }
        button { background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
        button:hover { background-color: #218838; }
        a.back-btn { display: inline-block; margin-bottom: 20px; padding: 10px 20px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 5px; }
        .success { color: #155724; background-color: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .error { color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
    </style>
</head>
<body>

<a href="dashboard.php" class="back-btn">← Back to Dashboard</a>

<h1>Ready to Adopt?</h1>

<div class="form-box">
    <?php if ($success_message): ?>
        <div class="success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <?php if (count($available_pets) > 0): ?>
        <form action="adopt_pet.php" method="POST">
            <label for="pet_id"><strong>Select a pet to adopt:</strong></label><br>

            <select name="pet_id" id="pet_id" required>
                <option value="" disabled selected>-- Choose your furrrever friend --</option>

                <?php foreach ($available_pets as $pet): ?>
                    <option value="<?php echo $pet['pet_id']; ?>">
                        <?php echo htmlspecialchars($pet['name']); ?> the <?php echo htmlspecialchars($pet['breed']); ?> (<?php echo htmlspecialchars($pet['species']); ?>)
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Adopt this Pet!</button>
        </form>
    <?php else: ?>
        <p>Sorry, there are no pets available for adoption right now. Check back later!</p>
    <?php endif; ?>
</div>

</body>
</html>