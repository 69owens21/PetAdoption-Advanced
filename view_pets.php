<?php
session_start();
require 'db_connect.php'; // <--- This is the magic line that brings in $pdo!

// 1. Fetch the pets from PostgreSQL using a JOIN to get the species name (Cat/Dog)
$stmt = $pdo->query("
    SELECT pets.name, pets.breed, pets.age, pets.gender, pet_types.name AS species 
    FROM pets 
    INNER JOIN pet_types ON pets.type_id = pet_types.type_id 
    WHERE adoption_status = 'Available'
    ORDER BY species, pets.name
");
$available_pets = $stmt->fetchAll(PDO::FETCH_ASSOC); // Grab all rows as an array
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Available Pets</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background-color: #f4f4f9; padding: 20px; }
        table { width: 80%; margin: 0 auto; border-collapse: collapse; background: white; box-shadow: 0px 0px 10px gray; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #007BFF; color: white; }
        a.back-btn { display: inline-block; margin-bottom: 20px; padding: 10px 20px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>

<a href="dashboard.php" class="back-btn">← Back to Dashboard</a>

<h1>Pets Available for Adoption</h1>

<table>
    <tr>
        <th>Name</th>
        <th>Species</th>
        <th>Breed</th>
        <th>Gender</th>
        <th>Age</th>
    </tr>

    <?php foreach ($available_pets as $pet): ?>
        <tr>
            <td><?php echo htmlspecialchars($pet['name']); ?></td>
            <td><?php echo htmlspecialchars($pet['species']); ?></td>
            <td><?php echo htmlspecialchars($pet['breed']); ?></td>
            <td><?php echo htmlspecialchars($pet['gender']); ?></td>
            <td><?php echo htmlspecialchars($pet['age']); ?></td>
        </tr>
    <?php endforeach; ?>
</table>

</body>
</html>