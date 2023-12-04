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
	$invoiceCode = $_POST['invoiceCode'];
	$newStatus = $_POST['newStatus'];
	
	$q = "UPDATE Uzsakymas SET Busena = '$newStatus' WHERE Saskaitos_numeris = '$invoiceCode'";
        $result = $database->query($q);
	
	if ($result) {
        $_SESSION['updateStatusMessage'] = "Sėkmingai atnaujinote būseną!";
    } else {
        $_SESSION['updateStatusMessage'] = "Nepavyko atnaujinti būsenos!";
    }
	
	header("Location: adminOrders.php");
	exit();
}




echo "<div style='text-align: center;'><h3>Laukiami užsakymai</h3></div>";

        
$q = "SELECT Saskaitos_numeris, Data, Busena, Vardas, Pavarde, El_pastas FROM Uzsakymas
        INNER JOIN Naudotojas ON Naudotojas.id_Naudotojas = Uzsakymas.fk_Naudotojasid_Naudotojas
        WHERE Busena != 3
        ORDER BY Data ";

$notDoneOrders = $database->query($q);

		if (!$notDoneOrders || (mysqli_num_rows($notDoneOrders) < 1)) {
		echo "<div>Neturite laukiamų užsakymų</div>";
		} else {
			$num_rows = mysqli_num_rows($notDoneOrders);

			for ($i = 0; $i < $num_rows; $i++) {
                                
                        echo '<table class="styled-table" align="left" border="1" cellspacing="0" cellpadding="3">';
			echo "<tr style='background-color: lightyellow;'><td>Sąskaitos numeris</td><td>Užsakymo data</td><td>Būsena</td><td>Pirkėjas</td><td>Pirkėjo E-paštas</td><td>Veiksmai</td></tr>\n";
			$order = mysqli_fetch_assoc($notDoneOrders);
                        $invoiceCode = $order["Saskaitos_numeris"]; 
                        $date = $order["Data"];
                        $status = $order["Busena"];   
                        $BName = $order["Vardas"];
                        $Bsurname = $order["Pavarde"];
                        $Bemail = $order["El_pastas"];				
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
                        echo "<td>" . $BName ." ". $Bsurname . "</td>"; 
                        echo "<td>" . $Bemail . "</td>";   

			echo '<td>
				<form action="" method="POST">
                                    <input type="hidden" name="invoiceCode" value="' . $invoiceCode . '">
                                    <select name="newStatus" class="styled-select">
                                        <option value="1">Pateiktas</option>
                                        <option value="2">Priimtas</option>
                                        <option value="3">Įvykdytas</option>
                                    </select>
                                    <button type="submit" name="updateStatus" class="customButton">Atnaujinti būseną</button>
                                </form>
			        </td>';
			echo "</tr>";
                        #echo "</table>";
                                
                        $q = "SELECT Krepselio_dalis.Kiekis, Krepselio_dalis.Busena, Lektuvo_dalis.Pavadinimas, Modelis, Tiekejo_imone.Pavadinimas as TPav, Imones_kodas  FROM Uzsakymas
                                INNER JOIN Krepselio_dalis on Krepselio_dalis.fk_Uzsakymasid_Uzsakymas = Uzsakymas.id_Uzsakymas
                                INNER JOIN Lektuvo_dalis on Lektuvo_dalis.id_Lektuvo_dalis = Krepselio_dalis.fk_Lektuvo_dalisid_Lektuvo_dalis
                                INNER JOIN Tiekejo_imone on Tiekejo_imone.id_Tiekejo_imone = Lektuvo_dalis.fk_Tiekejo_imoneid_Tiekejo_imone
                                WHERE Uzsakymas.Saskaitos_numeris = '$invoiceCode'";
                              
                        $parts = $database->query($q);
                        $num_parts_rows = mysqli_num_rows($parts);

                                
                        #echo '<table class="styled-table" align="left" border="1" cellspacing="0" cellpadding="3">';
                        echo "<tr style='background-color: lightgreen;'><td>Prekės pavadinimas</td><td>Modelis</td><td>Kiekis</td><td>Būsena</td><td>Pardavėjas</td><td>Pardavėjo įmonės kodas</td></tr>\n";
 
			for ($j = 0; $j < $num_parts_rows; $j++) {        
                               $part = mysqli_fetch_assoc($parts);
                                $amount = $part["Kiekis"]; 
                                $status = $part["Busena"];
                                $partName = $part["Pavadinimas"];
                                $partModel = $part["Modelis"];
                                $seller = $part["TPav"];
                                $sellerCode = $part["Imones_kodas"];
                                
                                echo "<tr>";
                                echo "<td>" . $partName . "</td>";
                                echo "<td>" . $partModel . "</td>";
                                echo "<td>" . $amount . "</td>";
                                if($status == 1) {
                                        echo "<td>Pateiktas</td>";
                                } else if($status == 2) {
                                        echo "<td>Priimtas</td>";
                                } else if($status == 3) {
                                        echo "<td>Įvykdytas</td>";
                                } 
                                echo "<td>" . $seller . "</td>";
                                echo "<td>" . $sellerCode . "</td>";
                                echo "</tr>";
		        }
                        echo "</table>";
		
		}
        }




echo "<div style='text-align: center;'><h3>Atlikti užsakymai</h3></div>";

$username = $_SESSION['username'];

$q = "SELECT Saskaitos_numeris, Data, Busena, Vardas, Pavarde, El_pastas FROM Uzsakymas
        INNER JOIN Naudotojas ON Naudotojas.id_Naudotojas = Uzsakymas.fk_Naudotojasid_Naudotojas
        WHERE Busena = 3
        ORDER BY Data DESC";

$result = $database->query($q);

		if (!$result || (mysqli_num_rows($result) < 1)) {
		echo "<div>Neturite atliktų užsakymų</div>";
		} else {
			$num_rows = mysqli_num_rows($result);

			for ($i = 0; $i < $num_rows; $i++) {
                                
                        echo '<table class="styled-table" align="left" border="1" cellspacing="0" cellpadding="3">';
			echo "<tr style='background-color: lightyellow;'><td>Sąskaitos numeris</td><td>Užsakymo data</td><td>Pirkėjas</td><td>Pirkėjo E-paštas</td></tr>\n";
			$order = mysqli_fetch_assoc($result);
                        $invoiceCode = $order["Saskaitos_numeris"]; 
                        $date = $order["Data"];
                        $status = $order["Busena"];   
                        $BName = $order["Vardas"];
                        $Bsurname = $order["Pavarde"];
                        $Bemail = $order["El_pastas"];				
			echo "<tr>";
			echo "<td>" . $invoiceCode . "</td>";
                        echo "<td>" . $date . "</td>";           
                        echo "<td>" . $BName ." ". $Bsurname . "</td>"; 
                        echo "<td>" . $Bemail . "</td>";   
			echo "</tr>";
                                
                        $q = "SELECT Krepselio_dalis.Kiekis, Krepselio_dalis.Busena, Lektuvo_dalis.Pavadinimas, Modelis, Tiekejo_imone.Pavadinimas as TPav, Imones_kodas  FROM Uzsakymas
                                INNER JOIN Krepselio_dalis on Krepselio_dalis.fk_Uzsakymasid_Uzsakymas = Uzsakymas.id_Uzsakymas
                                INNER JOIN Lektuvo_dalis on Lektuvo_dalis.id_Lektuvo_dalis = Krepselio_dalis.fk_Lektuvo_dalisid_Lektuvo_dalis
                                INNER JOIN Tiekejo_imone on Tiekejo_imone.id_Tiekejo_imone = Lektuvo_dalis.fk_Tiekejo_imoneid_Tiekejo_imone
                                WHERE Uzsakymas.Saskaitos_numeris = '$invoiceCode'";
                              
                        $parts = $database->query($q);
                        $num_parts_rows = mysqli_num_rows($parts);

                        echo "<tr style='background-color: lightgreen;'><td>Prekės pavadinimas</td><td>Modelis</td><td>Kiekis</td><td>Pardavėjas</td></tr>\n";
 
			for ($j = 0; $j < $num_parts_rows; $j++) {        
                               $part = mysqli_fetch_assoc($parts);
                                $amount = $part["Kiekis"]; 
                                $status = $part["Busena"];
                                $partName = $part["Pavadinimas"];
                                $partModel = $part["Modelis"];
                                $seller = $part["TPav"];
                                $sellerCode = $part["Imones_kodas"];
                                
                                echo "<tr>";
                                echo "<td>" . $partName . "</td>";
                                echo "<td>" . $partModel . "</td>";
                                echo "<td>" . $amount . "</td>";
                                echo "<td>" . $seller . " " . $sellerCode . "</td>";
                                echo "</tr>";
		        }
                        echo "</table>";
		
		}
        }
}

?>