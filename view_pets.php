<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['logged_in_user'])) {
    header("Location: index.php");
    exit();
}

// Notice we added pets.image_url to our SELECT statement!
$stmt = $pdo->query("
    SELECT pets.pet_id, pets.name, pets.breed, pets.age, pets.gender, pets.image_url, pet_types.name AS species 
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
    <title>Available Pets</title>
    <style>
        /* General Page Styling */
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; margin: 0; padding: 20px; text-align: center; }
        h1 { color: #333; margin-bottom: 30px; }
        a.back-btn { display: inline-block; margin-bottom: 20px; padding: 10px 20px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
        a.back-btn:hover { background-color: #5a6268; }

        /* The CSS Grid that holds the cards */
        .pet-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            max-width: 1000px;
            margin: 0 auto;
        }

        /* The Pet Card Styling */
        .pet-card {
            background: white;
            border-radius: 15px;
            overflow: hidden; /* Keeps the image inside the rounded corners */
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s; /* Cute hover animation */
        }
        .pet-card:hover {
            transform: translateY(-5px); /* Makes the card jump up slightly when hovered */
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }

        /* The Image Styling */
        .pet-image {
            width: 100%;
            height: 200px;
            object-fit: cover; /* Ensures the picture fills the box without stretching */
        }

        /* The Text inside the Card */
        .pet-info {
            padding: 15px;
            text-align: left;
        }
        .pet-info h2 { margin: 0 0 10px 0; color: #007BFF; font-size: 24px; }
        .pet-info p { margin: 5px 0; color: #555; }
        .badge { display: inline-block; background: #e2e3e5; padding: 5px 10px; border-radius: 20px; font-size: 12px; margin-top: 10px; }
    </style>
</head>
<body>

<a href="dashboard.php" class="back-btn">← Back to Dashboard</a>

<h1>Meet Your Furrrever Friend</h1>

<div class="pet-grid">
    <?php foreach ($available_pets as $pet): ?>
        <div class="pet-card">

            <img src="<?php echo htmlspecialchars($pet['image_url']); ?>" alt="Picture of <?php echo htmlspecialchars($pet['name']); ?>" class="pet-image">

            <div class="pet-info">
                <h2><?php echo htmlspecialchars($pet['name']); ?></h2>
                <p><strong>Breed:</strong> <?php echo htmlspecialchars($pet['breed']); ?></p>
                <p><strong>Age:</strong> <?php echo htmlspecialchars($pet['age']); ?> years old</p>
                <p><strong>Gender:</strong> <?php echo htmlspecialchars($pet['gender']); ?></p>

                <span class="badge"><?php echo htmlspecialchars($pet['species']); ?></span>
            </div>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>