<?php
require_once("include/database.php");

// Create an instance of the MySQLDB class
$database = new MySQLDB;

function DeletePart($database, $partId) {
    $q = "DELETE FROM Lektuvo_dalis WHERE id_Lektuvo_dalis = '$partId'";
    $result = $database->query($q);

    if ($result) {
        return "Ištrinta sėkmingai";
    } else {
        return "Nepavyko ištrinti";
    }
}

if (isset($_POST['redirect'])) {
    header("Location: createPart.php");
    exit;
}

if (isset($_POST['redirectToOrders'])) {
    header("Location: orderedParts.php");
    exit;
}

if (isset($_SESSION['delete_message'])) {
    echo "<div>" . $_SESSION['delete_message'] . "</div>";
    unset($_SESSION['delete_message']);
}

$fullUser = $database->getUserInfo($_SESSION['username']);
$companyId = $fullUser['fk_Tiekejo_imoneid_Tiekejo_imone'];

$q = "SELECT * FROM Lektuvo_dalis WHERE fk_Tiekejo_imoneid_Tiekejo_imone = '$companyId'";
$result = $database->query($q);


echo "<form method='post' action=''>";
echo "<input type='submit' name='redirect' value='Sukurti dalį' class='customButton'>";
echo "</form>";

echo "<form method='post' action=''>";
echo "<input type='submit' name='redirectToOrders' value='Peržiūrėti užsakymus' class='customButton'>";
echo "</form>";


if (!$result || (mysqli_num_rows($result) < 1)) {
    echo "<div>Parduodamų dalių nerasta</div>";
} else {
    $num_rows = mysqli_num_rows($result);
    echo "<table align=\"left\" border=\"1\" cellspacing=\"0\" cellpadding=\"3\">\n";
    echo "<tr><td>Pavadinimas</td><td>Gamintojas</td><td>Modelis</td><td>Kaina</td><td>Pristatymo laikas</td><td>Kiekis</td><td>Veiksmai</td></tr>\n";

    for ($i = 0; $i < $num_rows; $i++) {
        $row = mysqli_fetch_assoc($result);
        $name = $row["Pavadinimas"];
        $manufacturer = $row["Gamintojas"];
        $model = $row["Modelis"];
        $price = $row["Kaina"];
        $delivery = $row["Pristatymo_laikas"];
        $amount = $row["Kiekis"];

        $partId = $row["id_Lektuvo_dalis"];

        echo "<tr>";
        echo "<td>" . $name . "</td>";
        echo "<td>" . $manufacturer . "</td>";
        echo "<td>" . $model . "</td>";
        echo "<td>" . $price . "</td>";
        echo "<td>" . $delivery . "</td>";
        echo "<td>" . $amount . "</td>";
        echo "<td>";
        // Add a form with a hidden input to submit the partId to DeletePart function
        echo "<form method='post'>";
        echo "<input type='hidden' name='partId' value='$partId'>";
        echo "<input type='submit' name='delete' value='Ištrinti' style='margin-top: 10px; padding: 10px 20px; background-color: #007bff; color: #fff; border: none; border-radius: 5px; cursor: pointer;'>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";

	if (isset($_POST['delete'])) {
		$partIdToDelete = $_POST['partId'];
		$deleteResult = DeletePart($database, $partIdToDelete);

		// Store the message in a session variable
		$_SESSION['delete_message'] = $deleteResult;

		// Redirect to index.php
		header("Location: index.php");
		exit;
	}
}
?>
