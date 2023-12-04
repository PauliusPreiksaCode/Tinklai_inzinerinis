<?php
require_once("include/database.php");

// Create an instance of the MySQLDB class
$database = new MySQLDB;

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

echo "<form method='post'>";
echo "<input type='submit' name='myOrders' value='Peržiūrėti užsakymus' class='customButton'>";
echo "</form>";


echo "<form method='post' class='styled-form'>";
echo "<input type='text' name='name' class='styled-input'>";
echo "<input type='submit' name='filter' value='Filtruoti' class='customButton'>";
echo "</form>";

if(isset($_SESSION['CartError'])) {
 echo "<div style='color: red'>" . $_SESSION['CartError'] ."</div>";
 unset($_SESSION['CartError']);
}

if(isset($_SESSION['CartSucc'])) {
 echo "<div style='color: green'>" . $_SESSION['CartSucc'] ."</div>";
 unset($_SESSION['CartSucc']);
}

function Filter($database, $filteredName) {
    
    if (strlen($filteredName) == 0) {
        if(isset($_SESSION['filter'])) {
            unset($_SESSION['filter']);
        }
    } else {
        $_SESSION['filter'] = true;
    }
    
	if(isset($_SESSION['filter'])){

        $q = "SELECT DISTINCT Tiekejo_imone.Pavadinimas as pavadinimas  
                FROM Lektuvo_dalis 
                INNER JOIN Tiekejo_imone ON fk_Tiekejo_imoneid_Tiekejo_imone = id_Tiekejo_imone 
                WHERE Lektuvo_dalis.Pavadinimas LIKE '%" . $filteredName . "%'";

        $uniqueCompanies = $database->query($q);
        $companiesCount = mysqli_num_rows($uniqueCompanies);

        for ($i = 0; $i < $companiesCount; $i++) {

        $company = mysqli_fetch_assoc($uniqueCompanies);
        $cname = $company["pavadinimas"];
        echo "<div style='text-align: center;'><h3>". $cname ."</h3></div>";

        $q = "SELECT Lektuvo_dalis.*
                FROM Lektuvo_dalis 
                INNER JOIN Tiekejo_imone ON fk_Tiekejo_imoneid_Tiekejo_imone = id_Tiekejo_imone 
                WHERE Lektuvo_dalis.Pavadinimas LIKE '%" . $filteredName . "%'
                AND Tiekejo_imone.Pavadinimas = '$cname'";

        $filteredParts = $database->query($q);
        $partsCount = mysqli_num_rows($filteredParts);

        echo '<table class="styled-table" align="left" border="1" cellspacing="0" cellpadding="3">';
        echo "<tr><td>Pavadinimas</td><td>Gamintojas</td><td>Modelis</td><td>Kaina</td><td>Pristatymo laikas</td><td>Kiekis</td><td>Veiksmai</td></tr>\n";

            for ($j = 0; $j < $partsCount; $j++) {
                    $row = mysqli_fetch_assoc($filteredParts);
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
                    echo "<input type='number' name='count' class='styled-input'>";
                    echo "<input type='submit' name='addToCart' value='Į krepšelį' class='customButton'>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
                echo "</table>";
        }
    }
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
    
    header("Location: index.php");
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

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

function createOrder($database) {
    
    $invoiceNumber = generateRandomString(10);
    $userId = $_SESSION['userid'];
    
    $q = "INSERT INTO Uzsakymas (Saskaitos_numeris, Data, Busena, id_Uzsakymas, fk_Naudotojasid_Naudotojas) 
       VALUES ('$invoiceNumber', NOW(), '1', NULL, '$userId')";
    
    $database->query($q);
    
    $q = " SELECT id_Uzsakymas FROM Uzsakymas WHERE Saskaitos_numeris = '$invoiceNumber'";
    $result = $database->query($q);
    
    $dbarray = mysqli_fetch_array($result);
    $orderId = $dbarray['id_Uzsakymas'];
    
    foreach ($_SESSION['cart'] as $item) {
        $amount = $item->partsCount;
        $part = $item->partId;
            
        $q = "INSERT INTO Krepselio_dalis (Kiekis, id_Krepselio_dalis, fk_Lektuvo_dalisid_Lektuvo_dalis, fk_Uzsakymasid_Uzsakymas, Busena) 
            VALUES ('$amount', NULL, '$part', '$orderId', 1)";
        $database->query($q);
        
        $q = "UPDATE Lektuvo_dalis
                SET Kiekis = Kiekis - $amount
                WHERE id_Lektuvo_dalis = $part";
                
        $database->query($q);
    }
    $_SESSION['cart'] = array();   
    $_SESSION['CartSucc'] = "Užsakymas sėkmingai pateiktas!";
    header("Location: index.php");
    exit();
}


if (isset($_POST['filter'])) {
		$name = $_POST['name'];
		Filter($database, $name);
}

if (isset($_POST['myOrders'])) {
    unset($_SESSION['cart']);
    header("Location: myOrders.php");
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
        header("Location: index.php");
        exit();
}

if (isset($_POST['createOrder'])) {
		createOrder($database);
}


if(!isset($_SESSION['filter'])) {

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
                    echo "<input type='number' name='count' class='styled-input'>";
                    echo "<input type='submit' name='addToCart' value='Į krepšelį' class='customButton'>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
                echo "</table>";
        }	
    }
}


echo "<div class='cartwrapper'>";
echo "<div style='text-align: center;'><h3>Krepšelis</h3></div>";

		
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
echo "<input type='submit' name='createOrder' value='Užsakyti' class='customButton'>";
echo "</form>";
echo "</div>";

echo '<div style="border-top: 1px solid #000;"></div>';
echo "<div style='text-align: center;'><h3>Prekių palyginimas</h3></div>";
$q = "SELECT Pavadinimas, id_Lektuvo_dalis FROM Lektuvo_dalis";
$result = $database->query($q);

echo "<form method='post'>";
if ($result->num_rows > 0) {
    echo '<select name="first" class="styled-select" style="margin-right: 10px;">';
    while ($row = $result->fetch_assoc()) {
        $value = $row["id_Lektuvo_dalis"];
        $text = $row["Pavadinimas"];
        echo '<option value="' . $value . '">' . $text . '</option>';
    }
    echo '</select>';
}

$result = $database->query($q);
if ($result->num_rows > 0) {
    echo '<select name="second" class="styled-select">';
    while ($row = $result->fetch_assoc()) {
        $value = $row["id_Lektuvo_dalis"];
        $text = $row["Pavadinimas"];
        echo '<option value="' . $value . '">' . $text . '</option>';
    }
    echo '</select>';
}
echo "<input type='submit' name='compare' value='Palyginti' class='customButton'>";
echo "</form>";

if (isset($_POST["compare"])) {
    $first = $_POST["first"];
    $second = $_POST["second"];
    
    $q = "SELECT * FROM Lektuvo_dalis WHERE id_Lektuvo_dalis = '$first'";
    $result1 = $database->query($q);
    $q = "SELECT * FROM Lektuvo_dalis WHERE id_Lektuvo_dalis = '$second'";
    $result2 = $database->query($q);
    
    $item1 = mysqli_fetch_assoc($result1);
    $item2 = mysqli_fetch_assoc($result2);

    echo '<table class="styled-table" align="left" border="1" cellspacing="0" cellpadding="3">';
    echo "<tr><td>Pavadinimas<td>". $item1["Pavadinimas"] ."</td><td>". $item2["Pavadinimas"] ."</td></tr>";
    echo "<tr><td>Gamintojas<td>". $item1["Gamintojas"] ."</td><td>". $item2["Gamintojas"] ."</td></tr>";
    echo "<tr><td>Modelis<td>". $item1["Modelis"] ."</td><td>". $item2["Modelis"] ."</td></tr>";
    echo "<tr><td>Kaina<td>". $item1["Kaina"] ."</td><td>". $item2["Kaina"] ."</td></tr>";
    echo "<tr><td>Pristatymo laikas<td>". $item1["Pristatymo_laikas"] ."</td><td>". $item2["Pristatymo_laikas"] ."</td></tr>";
    echo "</table>";
}

?>