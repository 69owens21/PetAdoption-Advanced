<?php
session_start();
require 'db_connect.php';

// Security check: Make sure they are logged in
if (!isset($_SESSION['logged_in_user'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['logged_in_user'];

// 1. Fetch the user's specific adoption history using INNER JOIN
// We use a prepared statement because we are injecting the $username
$stmt = $pdo->prepare("
    SELECT pets.name, pets.breed, adoptions.adoption_date 
    FROM adoptions 
    INNER JOIN pets ON adoptions.pet_id = pets.pet_id 
    WHERE adoptions.username = :username
    ORDER BY adoptions.adoption_date DESC
");
$stmt->execute(['username' => $username]);
$history = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Adoption History</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background-color: #f4f4f9; padding: 20px; }
        table { width: 80%; margin: 0 auto; border-collapse: collapse; background: white; box-shadow: 0px 0px 10px gray; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #17a2b8; color: white; }
        a.back-btn { display: inline-block; margin-bottom: 20px; padding: 10px 20px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 5px; }
        .empty-message { background: white; padding: 20px; border-radius: 10px; width: 400px; margin: 0 auto; box-shadow: 0px 0px 10px gray; color: #555; }
    </style>
</head>
<body>

<a href="dashboard.php" class="back-btn">← Back to Dashboard</a>

<h1>Your Adoption History</h1>

<?php if (count($history) > 0): ?>
    <table>
        <tr>
            <th>Pet Name</th>
            <th>Breed</th>
            <th>Adoption Date</th>
        </tr>
        <?php foreach ($history as $record): ?>
            <tr>
                <td><?php echo htmlspecialchars($record['name']); ?></td>
                <td><?php echo htmlspecialchars($record['breed']); ?></td>
                <td><?php echo htmlspecialchars($record['adoption_date']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <div class="empty-message">
        <p>You haven't adopted any pets yet.</p>
        <a href="view_pets.php">Click here to browse available pets!</a>
    </div>
<?php endif; ?>

</body>
</html>