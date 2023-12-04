<?php
require_once("include/database.php");

// Create an instance of the MySQLDB class
$database = new MySQLDB;

// Check if the form is submitted
if (isset($_POST['update'])) {
    $selectedValue = $_POST['selectedValue'];
	
	$username = $_SESSION['username'];
	echo $username;

    $q = "UPDATE Naudotojas SET fk_Tiekejo_imoneid_Tiekejo_imone = '$selectedValue' WHERE El_pastas = '$username'";
    if ($database->query($q)) {
    // Query executed successfully
    header("Location: index.php");
    exit;
	} else {
		// Query execution failed, handle the error.
		echo "Error: " . $database->error; // Display the error message for debugging.
		echo $selectedValue;
	}
}

// Retrieve the list of options for the dropdown
$q = "SELECT Pavadinimas, id_Tiekejo_imone FROM Tiekejo_imone";
$result = $database->query($q);

// Fetch the data and store it in an array
$options = array();
while ($row = mysqli_fetch_assoc($result)) {
    $options[$row['id_Tiekejo_imone']] = $row['Pavadinimas'];
}
?>


    <form method="post" style="text-align: center;">
		<label for="selectedValue">Pasirinkti įmonę:</label>
		<select name="selectedValue" id="selectedValue" style="padding: 5px; font-size: 14px;">
			<?php
			foreach ($options as $imonesKodas => $pavadinimas) {
				echo "<option value='$imonesKodas'>$pavadinimas</option>";
			}
			?>
		</select>
		<br>
		<input type="submit" name="update" value="Priskirti" style="margin-top: 10px; padding: 10px 20px; background-color: #007bff; color: #fff; border: none; border-radius: 5px; cursor: pointer;">
	</form>

