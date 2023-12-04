<?php
require_once("include/database.php");

// Create an instance of the MySQLDB class
$database = new MySQLDB;

if (isset($_POST["updateRole"])) {
    $userId = $_POST["userId"];
    $newRole = $_POST["newRole"];

    // Perform the database update
    $q = "UPDATE Naudotojas SET Role = '$newRole' WHERE id_Naudotojas = $userId";
    $result = $database->query($q);

    if ($result) {
        echo "Role updated successfully.";
    } else {
        echo "Error updating role: ";
    }
} else {
    echo "Invalid parameters.";
}

header("Location: index.php");
?>
