<?php
session_start();
session_destroy(); // This deletes their login memory!
header("Location: index.php"); // Send them back to the login screen
exit();
?>