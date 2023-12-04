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

if (isset($_POST['backToIndex'])) {
        unset($_SESSION['cart']);
        unset($_SESSION['fillExecuted']);
        unset($_SESSION['editInvoice']);
        unset($_SESSION['invoiceId']);
        
        header("Location: index.php");
}

?>

<form method='post'>
        <input type='submit' name='backToIndex' value='Atgal į pagrindinį' class='customButton'>
</form>

<?php





if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

if(isset($_SESSION['CartError'])) {
 echo "<div style='color: red'>" . $_SESSION['CartError'] ."</div>";
 unset($_SESSION['CartError']);
}

if(isset($_SESSION['CartSucc'])) {
 echo "<div style='color: green'>" . $_SESSION['CartSucc'] ."</div>";
 unset($_SESSION['CartSucc']);
}


function CheckForAvailability($database, $partId, $count) {
    $totalCount = $count;
    
    foreach ($_SESSION['cart'] as $item) {
        $amount = $item->partsCount;
        $part = $item->partId;
        
        if($part == $partId) {
            $totalCount = $totalCount + $amount; 
        }
    }
    
    $q = "SELECT Kiekis FROM Lektuvo_dalis WHERE id_Lektuvo_dalis = '$partId'";
    $result = $database->query($q);
    $p = mysqli_fetch_array($result);
    $c = $p["Kiekis"];
    
    if ($c < $totalCount) {
        return false;   
    }
    
    return true;
}

function addToCart(&$cart, $partId, $count, $name, $price, $database) {
    $item = new PartItem($name, $count, $partId, $price);
    
    $rez = CheckForAvailability($database, $partId, $count);
    
    if($rez && $count > 0) {
        $_SESSION['cart'][] = $item;
        $_SESSION['CartSucc'] = "Prekė sėkmingai pridėta!";
    } else if(!$rez) {
        $_SESSION['CartError'] = "Pasirinktas per didelis prekės kiekis!";
    } else if($count <= 0) {
        $_SESSION['CartError'] = "Prašome pasirinkti prekės kiekį didesnį už 0!";
    }
    
    header("Location: myOrders.php");
    exit();
        
}

function removeFromCart($index)  {
    unset($_SESSION['cart'][$index]);   
}

function reindexArray($array) {
    $newArray = array();

    foreach ($array as $item) {
        $newArray[] = $item;
    }

    return $newArray;
}

function fillCart($database, $invoiceId) {
    
    if(!isset($_SESSION['fillExecuted'])){
    
        $q = "SELECT Krepselio_dalis.Kiekis as pKiekis, Lektuvo_dalis.* FROM Krepselio_dalis 
            INNER JOIN Lektuvo_dalis ON fk_Lektuvo_dalisid_Lektuvo_dalis = id_Lektuvo_dalis 
            WHERE fk_Uzsakymasid_Uzsakymas = '$invoiceId'";

        $parts = $database->query($q);
        $partsCount = mysqli_num_rows($parts);

        for ($j = 0; $j < $partsCount; $j++) {
            $row = mysqli_fetch_assoc($parts);
            $name = $row["Pavadinimas"];
            $price = $row["Kaina"];
            $amount = $row["pKiekis"];
            $partId = $row["id_Lektuvo_dalis"];

            $item = new PartItem($name, $amount, $partId, $price);
            $_SESSION['cart'][] = $item;
        }

        $_SESSION['fillExecuted'] = true;
    }
}

if (isset($_POST['removeInvoice'])) {
    $invoiceId = $_POST['invoice'];
    
    $q = "SELECT * from Krepselio_dalis WHERE fk_Uzsakymasid_Uzsakymas = '$invoiceId'";
        $partsToDelete = $database->query($q);
        $pCount = mysqli_num_rows($partsToDelete);
        for ($i = 0; $i < $pCount; $i++) {
            $p = mysqli_fetch_assoc($partsToDelete);
            $pId = $p["fk_Lektuvo_dalisid_Lektuvo_dalis"];
            $pCount = $p["Kiekis"];    
            $q = "UPDATE Lektuvo_dalis SET Kiekis = Kiekis + $pCount WHERE id_Lektuvo_dalis = $pId";
            $database->query($q);
        }
    
    $q = "DELETE FROM Krepselio_dalis WHERE fk_Uzsakymasid_Uzsakymas = '$invoiceId'";
    $database->query($q);
    
    $q = "DELETE FROM Uzsakymas WHERE id_Uzsakymas = '$invoiceId'";
    $database->query($q);
        
    $_SESSION['CartSucc'] = "Užsakymas sėkmingai panaikintas!";
    header("Location: myOrders.php");
    exit();
}




if (isset($_POST['addToCart'])) {
	$partId = $_POST['partId'];
        $count = $_POST['count'];
        $name = $_POST['name'];
        $price = $_POST['price'];
        addToCart($_SESSION['cart'], $partId, $count, $name, $price, $database);
        $_SESSION['cart'] = array_values($_SESSION['cart']);
}

if (isset($_POST['removeFromCart'])) {
	$index = $_POST['index'];
	removeFromCart($index);
        $_SESSION['cart'] = reindexArray($_SESSION['cart']);
        $_SESSION['CartSucc'] = "Prekė sėkmingai išimta iš krepšelio!";
        header("Location: myOrders.php");
        exit();
}

if (isset($_POST['updateInvoice'])) {
	$index = $_POST['index'];
	unset($_SESSION['editInvoice']);
        unset($_SESSION['fillExecuted']);
    
        $q = "SELECT * from Krepselio_dalis WHERE fk_Uzsakymasid_Uzsakymas = '$index'";
        $partsToDelete = $database->query($q);
        $pCount = mysqli_num_rows($partsToDelete);
        for ($i = 0; $i < $pCount; $i++) {
            $p = mysqli_fetch_assoc($partsToDelete);
            $pId = $p["fk_Lektuvo_dalisid_Lektuvo_dalis"];
            $pCount = $p["Kiekis"];    
            $q = "UPDATE Lektuvo_dalis SET Kiekis = Kiekis + $pCount WHERE id_Lektuvo_dalis = $pId";
            $database->query($q);
        }
    
        $q = "DELETE FROM Krepselio_dalis WHERE fk_Uzsakymasid_Uzsakymas = '$index'";
        $database->query($q);
    
        foreach ($_SESSION['cart'] as $item) {
            $amount = $item->partsCount;
            $part = $item->partId;

            $q = "INSERT INTO Krepselio_dalis (Kiekis, id_Krepselio_dalis, fk_Lektuvo_dalisid_Lektuvo_dalis, fk_Uzsakymasid_Uzsakymas, Busena) 
                VALUES ('$amount', NULL, '$part', '$index', 1)";
            $database->query($q);

            $q = "UPDATE Lektuvo_dalis
                    SET Kiekis = Kiekis - $amount
                    WHERE id_Lektuvo_dalis = $part";

            $database->query($q);
        }
    
        unset($_SESSION['cart']);
        $_SESSION['CartSucc'] = "Užsakymas sėkmingai pakeistas!";
        header("Location: myOrders.php");
        exit();
}


if (!$session->logged_in) {
    header("Location: index.php");
} else {
	
	if (isset($_POST['edit'])) {
        $_SESSION['invoiceId'] = $_POST['invoice'];
	$_SESSION['editInvoice'] = true;  
        
        
    }
    
    if (isset($_SESSION['editInvoice']) && $_SESSION['editInvoice']) {
        
        $invoiceId = $_SESSION['invoiceId'];
		
		$q = "SELECT * FROM Uzsakymas WHERE id_Uzsakymas = '$invoiceId'";
		$result = $database->query($q);
		$result = mysqli_fetch_assoc($result);
		$invoiceCode = $result["Saskaitos_numeris"];
        echo "<div style='text-align: center;'><h3>Sąskaitos kodas: ". $invoiceCode ."</h3></div>";
		
		fillCart($database, $invoiceId);
        
		
		echo '<table class="styled-table" align="left" border="1" cellspacing="0" cellpadding="3">';
		echo "<tr><td>Pavadinimas</td><td>Kaina</td><td>Kiekis</td><td>Veiksmai</td></tr>\n";
		$cartIndex = 0;
		foreach ($_SESSION['cart'] as $item) {
			echo "<tr>";
			echo "<td>" . $item->name . "</td>";
			echo "<td>" . $item->price . "</td>";
			echo "<td>" . $item->partsCount . "</td>";
			echo "<td>";
			echo "<form method='post'>";
			echo "<input type='hidden' name='index' value='$cartIndex'>";
			echo "<input type='submit' name='removeFromCart' value='Panaikinti' class='customButton'>";
			echo "</form>";
			echo "</td>";
			echo "</tr>";

			$cartIndex++;
		}
		echo "</table>";
        
        echo "<form method='post'>";
		echo "<input type='hidden' name='index' value='$invoiceId'>";
		echo "<input type='submit' name='updateInvoice' value='Atnaujinti' class='customButton'>";
		echo "</form>";
        
		$q = "SELECT * FROM Tiekejo_imone";
	$allCompanies = $database->query($q);

	if (!$allCompanies || (mysqli_num_rows($allCompanies) < 1)) {
		echo "<div>Parduodamų dalių nerasta</div>";
	} else {
		$companiesCount = mysqli_num_rows($allCompanies);

		for ($i = 0; $i < $companiesCount; $i++) {
			$company = mysqli_fetch_assoc($allCompanies);
			$cname = $company["Pavadinimas"];
			$cid = $company["id_Tiekejo_imone"];

            echo "<div style='text-align: center;'><h3>". $cname ."</h3></div>";

			$q = "SELECT * FROM Lektuvo_dalis WHERE fk_Tiekejo_imoneid_Tiekejo_imone = '$cid'";
			$companieParts = $database->query($q);
			$partsCount = mysqli_num_rows($companieParts);


			echo '<table class="styled-table" align="left" border="1" cellspacing="0" cellpadding="3">';
			echo "<tr><td>Pavadinimas</td><td>Gamintojas</td><td>Modelis</td><td>Kaina</td><td>Pristatymo laikas</td><td>Kiekis</td><td>Veiksmai</td></tr>\n";

				for ($j = 0; $j < $partsCount; $j++) {
					$row = mysqli_fetch_assoc($companieParts);
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
					echo "<form method='post'>";
					echo "<input type='hidden' name='partId' value='$partId'>";
					echo "<input type='hidden' name='name' value='$name'>";
					echo "<input type='hidden' name='price' value='$price'>";
					echo "<input type='number' name='count'>";
					echo "<input type='submit' name='addToCart' value='Į krepšelį' class='customButton'>";
					echo "</form>";
					echo "</td>";
					echo "</tr>";
				}
				echo "</table>";
			}	
		}
	}
	
    if(!isset($_SESSION['editInvoice'])) {
    
        echo "<div style='text-align: center;'><h3>Mano užsakymai</h3></div>";
		$userId = $_SESSION['userid'];
		$q = "SELECT * FROM Uzsakymas WHERE fk_Naudotojasid_Naudotojas = '$userId'";
		$result = $database->query($q);

		if (!$result || (mysqli_num_rows($result) < 1)) {
		echo "<div>Neturite atliktų užsakymų</div>";
		} else {
			echo '<table class="styled-table" align="left" border="1" cellspacing="0" cellpadding="3">';
			echo "<tr><td>Sąskaitos kodas</td><td>Užsakymo data</td><td>Būsena</td><td>Veiksmai</td></tr>\n";
			$num_rows = mysqli_num_rows($result);

			for ($i = 0; $i < $num_rows; $i++) {
			$row = mysqli_fetch_assoc($result);
			$invoiceCode = $row["Saskaitos_numeris"];
			$date = $row["Data"];
			$status = $row["Busena"];
			$invoiceId = $row["id_Uzsakymas"];

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

			echo "<td>";
            if($status == 1) {
				echo "<form method='post'>";
				echo "<input type='hidden' name='invoice' value='$invoiceId'>";
				echo "<input type='submit' name='edit' value='Redaguoti' class='customButton'>";
				echo "</form>";
				
                echo "<form method='post'>";
                echo "<input type='hidden' name='invoice' value='$invoiceId'>";
                echo "<input type='submit' name='removeInvoice' value='Atšaukti' class='customButton'>";
                echo "</form>";
            }
			echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
		}
    }
}




?>