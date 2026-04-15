<?php
session_start();
require 'db_connect.php';

// Security check: Make sure they are logged in
if (!isset($_SESSION['logged_in_user'])) {
    header("Location: index.php");
    exit();
}

// 1. Calculate live statistics using COUNT() and GROUP BY
// We use LEFT JOINs so that even if a species has 0 adoptions, it still shows up!
$stmt = $pdo->query("
    SELECT pet_types.name AS species, COUNT(adoptions.adoption_id) AS total_adoptions 
    FROM pet_types 
    LEFT JOIN pets ON pet_types.type_id = pets.type_id 
    LEFT JOIN adoptions ON pets.pet_id = adoptions.pet_id 
    GROUP BY pet_types.name
");
$stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shelter Statistics</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background-color: #f4f4f9; padding: 20px; }
        .stats-container { display: flex; justify-content: center; gap: 20px; margin-top: 20px; }
        .stat-card { background: white; padding: 30px; border-radius: 10px; width: 200px; box-shadow: 0px 0px 10px gray; }
        .stat-card h2 { margin: 0; font-size: 24px; color: #333; }
        .stat-card .number { font-size: 48px; font-weight: bold; color: #007BFF; margin-top: 10px; }
        a.back-btn { display: inline-block; margin-bottom: 20px; padding: 10px 20px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>

<a href="dashboard.php" class="back-btn">← Back to Dashboard</a>

<h1>Live Shelter Adoption Statistics</h1>
<p>Total adoptions broken down by species.</p>

<div class="stats-container">
    <?php foreach ($stats as $row): ?>
        <div class="stat-card">
            <h2><?php echo htmlspecialchars($row['species']); ?>s</h2>
            <div class="number"><?php echo htmlspecialchars($row['total_adoptions']); ?></div>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>