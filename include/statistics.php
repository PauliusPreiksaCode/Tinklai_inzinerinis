<?php
	require_once("include/database.php");
	$database = new MySQLDB;



	echo '<div class="section-title"><h3>Statistika</h3></div>';

	$q = "SELECT Uzsakymas.Saskaitos_numeris, Uzsakymas.Data, Uzsakymas.Busena, Krepselio_dalis.Kiekis, Naudotojas.Vardas, Naudotojas.Pavarde, Lektuvo_dalis.Pavadinimas, Lektuvo_dalis.Kaina, DATE_ADD(Uzsakymas.Data, INTERVAL Lektuvo_dalis.Pristatymo_laikas DAY) as Pristatymo_data FROM Uzsakymas 
			INNER JOIN Krepselio_dalis ON id_Uzsakymas = fk_Uzsakymasid_Uzsakymas
			INNER JOIN Naudotojas ON fk_Naudotojasid_Naudotojas = id_Naudotojas
			INNER JOIN Lektuvo_dalis ON fk_Lektuvo_dalisid_Lektuvo_dalis = id_Lektuvo_dalis";

	$result = $database->query($q);

	if (!$result || (mysqli_num_rows($result) < 1)) {
		echo "<div class='no-orders'>Nėra atliktų užsakymų</div>";
	} else {
		
		$totalCount = 0;
		$totalCost = 0;
		
		echo '<table class="styled-table" align="left" border="1" cellspacing="0" cellpadding="3">';
		echo "<tr><td>Sąskaitos kodas</td><td>Užsakymo data</td><td>Būsena</td><td>Prekė</td><td>Kiekis</td><td>Kaina</td><td>Užsakovas</td><td>Pristatymo data</td></tr>\n";
		$num_rows = mysqli_num_rows($result);
		for ($i = 0; $i < $num_rows; $i++) {
			$row = mysqli_fetch_assoc($result);
			$invoiceCode = $row["Saskaitos_numeris"];
			$date = $row["Data"];
			$status = $row["Busena"];
			$amount = $row["Kiekis"];
			$uname = $row["Vardas"];
			$usurname = $row["Pavarde"];
			$name = $row["Pavadinimas"];
			$price = $row["Kaina"];
			$delivery = $row["Pristatymo_data"];
			
			echo "<tr>";
			echo "<td>" . $invoiceCode . "</td>";
			echo "<td>" . $date . "</td>";
			if($status == 1) {
				echo "<td>Pateiktas</td>";
			} else if($status == 2) {
				echo "<td>Priimtas</td>";
			} else if($status == 3) {
				echo "<td>Įvykdytas</td>";
			}
			echo "<td>" . $name . "</td>";
			echo "<td>" . $amount . "</td>";
			echo "<td>" . $price . "</td>";
			echo "<td>" . $uname . " " . $usurname . "</td>";
			echo "<td>" . $delivery . "</td>";
			echo "</tr>";
			
			$totalCount = $totalCount + $amount;
			$totalCost = $totalCost + $amount * $price;
		}
		echo "</table>";
		echo "<div class='order-summary'>Užsakytų prekių skaičius: " . $totalCount . ", bendra užsakymų kaina: " . $totalCost . "</div>";
	}

?>