<?php
session_start();
require 'db_connect.php';

// Security check: Make sure they are logged in
if (!isset($_SESSION['logged_in_user'])) {
    header("Location: index.php");
    exit();
}

// We set up empty variables so the page doesn't crash before the user searches
$search_results = [];
$search_term = "";

// Check if the user clicked the "Search" button
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['breed'])) {

    // Grab what they typed into the search box
    $search_term = $_GET['breed'];

    // The SQL Query (Notice we use ILIKE just like in your Bash script!)
    $stmt = $pdo->prepare("
        SELECT pets.name, pets.breed, pets.age, pets.gender, pet_types.name AS species 
        FROM pets 
        INNER JOIN pet_types ON pets.type_id = pet_types.type_id 
        WHERE pets.breed ILIKE :search_term AND pets.adoption_status = 'Available'
    ");

    // We add the '%' wildcards around their search term so it finds partial matches
    $stmt->execute(['search_term' => '%' . $search_term . '%']);

    // Fetch the matching pets!
    $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Pets</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background-color: #f4f4f9; padding: 20px; }
        .search-box { background: white; padding: 20px; border-radius: 10px; width: 400px; margin: 0 auto 20px auto; box-shadow: 0px 0px 10px gray; }
        input, button { margin: 10px 0; padding: 10px; width: 90%; }
        button { background-color: #007BFF; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        table { width: 80%; margin: 0 auto; border-collapse: collapse; background: white; box-shadow: 0px 0px 10px gray; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #28a745; color: white; }
        a.back-btn { display: inline-block; margin-bottom: 20px; padding: 10px 20px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>

<a href="dashboard.php" class="back-btn">← Back to Dashboard</a>

<h1>Search for a Pet</h1>

<div class="search-box">
    <form action="search_pets.php" method="GET">
        <input type="text" name="breed" placeholder="Enter a breed (e.g., Tabby, Beagle)" required value="<?php echo htmlspecialchars($search_term); ?>">
        <button type="submit">Search</button>
    </form>
</div>

<?php if (isset($_GET['breed'])): ?>

    <?php if (count($search_results) > 0): ?>
        <h3>We found <?php echo count($search_results); ?> matching pet(s)!</h3>
        <table>
            <tr>
                <th>Name</th>
                <th>Species</th>
                <th>Breed</th>
                <th>Gender</th>
                <th>Age</th>
            </tr>
            <?php foreach ($search_results as $pet): ?>
                <tr>
                    <td><?php echo htmlspecialchars($pet['name']); ?></td>
                    <td><?php echo htmlspecialchars($pet['species']); ?></td>
                    <td><?php echo htmlspecialchars($pet['breed']); ?></td>
                    <td><?php echo htmlspecialchars($pet['gender']); ?></td>
                    <td><?php echo htmlspecialchars($pet['age']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <h3 style="color: red;">No available pets match that breed. Please try another search.</h3>
    <?php endif; ?>

<?php endif; ?>

</body>
</html>