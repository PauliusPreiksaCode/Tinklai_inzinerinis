<?php
include("include/session.php");
require_once("include/database.php");
$database = new MySQLDB;

?>

<head>
        <meta http-equiv="X-UA-Compatible" content="IE=9; text/html; charset=utf-8"/>
        <title>Inzinerinis projektas</title>
        <link href="include/styles.css" rel="stylesheet" type="text/css" />
</head>

<?php
include("include/header.php"); 
include("include/meniu.php");

if (isset($session) && $session->logged_in) {

if (isset($_POST['backToIndex'])) {       
        header("Location: index.php");
}

?>

<form method='post'>
        <input type='submit' name='backToIndex' value='Atgal' class='customButton'>
</form>

<?php

if(isset($_SESSION['updateStatusMessage'])) {
	echo "<div>". $_SESSION['updateStatusMessage'] ."</div>";
	unset($_SESSION['updateStatusMessage']);

}


if (isset($_POST['updateStatus'])) {
	$partId = $_POST['partId'];
	$newStatus = $_POST['newStatus'];
	
	$q = "UPDATE Krepselio_dalis SET Busena = '$newStatus' WHERE id_Krepselio_dalis = $partId";
    $result = $database->query($q);
	
	if ($result) {
        $_SESSION['updateStatusMessage'] = "Sėkmingai atnaujinote būseną!";
    } else {
        $_SESSION['updateStatusMessage'] = "Nepavyko atnaujinti būsenos!";
    }
	
	header("Location: orderedParts.php");
	exit();
}




echo "<div style='text-align: center;'><h3>Laukiami užsakymai</h3></div>";

$username = $_SESSION['username'];

$q = "SELECT Pavadinimas, Modelis, Krepselio_dalis.Kiekis, Krepselio_dalis.Busena, Saskaitos_numeris, Data, id_Krepselio_dalis FROM Naudotojas 
	INNER JOIN Lektuvo_dalis ON Naudotojas.fk_Tiekejo_imoneid_Tiekejo_imone = Lektuvo_dalis.fk_Tiekejo_imoneid_Tiekejo_imone 
	INNER JOIN Krepselio_dalis ON fk_Lektuvo_dalisid_Lektuvo_dalis = id_Lektuvo_dalis
	INNER JOIN Uzsakymas on id_Uzsakymas = fk_Uzsakymasid_Uzsakymas
	WHERE Naudotojas.El_pastas = '$username'
	AND Krepselio_dalis.Busena != 3
	ORDER BY Data DESC, Saskaitos_numeris";

$result = $database->query($q);

		if (!$result || (mysqli_num_rows($result) < 1)) {
		echo "<div>Neturite laukiamų užsakymų</div>";
		} else {
			echo '<table class="styled-table" align="left" border="1" cellspacing="0" cellpadding="3">';
			echo "<tr><td>Pavadinimas</td><td>Prekės kodas</td><td>Kiekis</td><td>Būsena</td><td>Sąskaitos numeris</td><td>Užsakymo data</td><td>Veiksmai</td></tr>\n";
			$num_rows = mysqli_num_rows($result);

			for ($i = 0; $i < $num_rows; $i++) {
			$row = mysqli_fetch_assoc($result);
			$itemName = $row["Pavadinimas"];
			$itemCode = $row["Modelis"];
			$amount = $row["Kiekis"];
			$status = $row["Busena"];
			$invoiceCode = $row["Saskaitos_numeris"];
			$date = $row["Data"];
			$partId = $row["id_Krepselio_dalis"];
				
			echo "<tr>";
			echo "<td>" . $itemName . "</td>";
			echo "<td>" . $itemCode . "</td>";
			echo "<td>" . $amount . "</td>";
			if($status == 1) {
				echo "<td>Pateiktas</td>";
			} else if($status == 2) {
				echo "<td>Priimtas</td>";
			} else if($status == 3) {
				echo "<td>Įvykdytas</td>";
			}
			echo "<td>" . $invoiceCode . "</td>";
			echo "<td>" . $date . "</td>";
			

			echo '<td>
				<form action="" method="POST">
                    <input type="hidden" name="partId" value="' . $partId . '">
                    <select name="newStatus" class="styled-select">
                        <option value="1">Pateiktas</option>
                        <option value="2">Priimtas</option>
                        <option value="3">Įvykdytas</option>
                    </select>
                    <button type="submit" name="updateStatus" class="customButton">Atnaujinti būseną</button>
                </form>
			  </td>';
			echo "</tr>";
		}
		echo "</table>";
		}




echo "<div style='text-align: center;'><h3>Atlikti užsakymai</h3></div>";

$username = $_SESSION['username'];

$q = "SELECT Pavadinimas, Modelis, Krepselio_dalis.Kiekis, Krepselio_dalis.Busena, Saskaitos_numeris, Data FROM Naudotojas 
	INNER JOIN Lektuvo_dalis ON Naudotojas.fk_Tiekejo_imoneid_Tiekejo_imone = Lektuvo_dalis.fk_Tiekejo_imoneid_Tiekejo_imone 
	INNER JOIN Krepselio_dalis ON fk_Lektuvo_dalisid_Lektuvo_dalis = id_Lektuvo_dalis
	INNER JOIN Uzsakymas on id_Uzsakymas = fk_Uzsakymasid_Uzsakymas
	WHERE Naudotojas.El_pastas = '$username'
	AND Krepselio_dalis.Busena = 3
	ORDER BY Data DESC, Saskaitos_numeris";

$result = $database->query($q);

		if (!$result || (mysqli_num_rows($result) < 1)) {
		echo "<div>Neturite atliktų užsakymų</div>";
		} else {
			echo '<table class="styled-table" align="left" border="1" cellspacing="0" cellpadding="3">';
			echo "<tr><td>Pavadinimas</td><td>Prekės kodas</td><td>Kiekis</td><td>Būsena</td><td>Sąskaitos numeris</td><td>Užsakymo Data</td></tr>\n";
			$num_rows = mysqli_num_rows($result);

			for ($i = 0; $i < $num_rows; $i++) {
			$row = mysqli_fetch_assoc($result);
			$itemName = $row["Pavadinimas"];
			$itemCode = $row["Modelis"];
			$amount = $row["Kiekis"];
			$status = $row["Busena"];
			$invoiceCode = $row["Saskaitos_numeris"];
			$date = $row["Data"];
				
			echo "<tr>";
			echo "<td>" . $itemName . "</td>";
			echo "<td>" . $itemCode . "</td>";
			echo "<td>" . $amount . "</td>";
			if($status == 1) {
				echo "<td>Pateiktas</td>";
			} else if($status == 2) {
				echo "<td>Priimtas</td>";
			} else if($status == 3) {
				echo "<td>Įvykdytas</td>";
			}
			echo "<td>" . $invoiceCode . "</td>";
			echo "<td>" . $date . "</td>";
			
			echo "</tr>";
		}
		echo "</table>";
		}
}

?>