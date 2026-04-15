<?php
$host = 'localhost';
$db = 'petadoption';
$user = 'postgres';
$pass = 'Gracie39$$';

try {
    // This creates a new PDO (PHP Data Object) connection to PostgreSQL
    $pdo = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);

    // This tells PDO to throw an error if something goes wrong so we can see it
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}
?>

