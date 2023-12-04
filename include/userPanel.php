<?php
global $database;

if (isset($session) && $session->logged_in) {
    
    
    if (isset($_POST['redirectToOrders'])) {
    header("Location: adminOrders.php");
    exit;
    }
    
    echo "<form method='post' action=''>";
    echo "<input type='submit' name='redirectToOrders' value='Peržiūrėti užsakymus' class='customButton'>";
    echo "</form>";
    
    
    
    $path = "";
    if (isset($_SESSION['path'])) {
        $path = $_SESSION['path'];
        unset($_SESSION['path']);
    }
	
	$q = "SELECT * FROM Naudotojas";
    $result = $database->query($q);
	$num_rows = mysqli_num_rows($result);
	
	if (!$result || ($num_rows < 0)) {
        echo "Error displaying info";
        return;
    }
	
    echo '<h1 style="text-align: center;">Naudotojų panelė</h1>';
	echo '<table class="styled-table" align="left" border="1" cellspacing="0" cellpadding="3">';
    echo "<tr><td>Nr</td><td>Vardas</td><td>Pavardė</td><td>E-paštas</td><td>Telefono numeris</td><td>Rolė</td><td>Tiekėjas</td><td>Veiksmai</td></tr>\n";
	
	
	for ($i = 0; $i < $num_rows; $i++) {
        $row = mysqli_fetch_assoc($result);
        $name = $row["Vardas"];
        $sirname = $row["Pavarde"];
        $email = $row["El_pastas"];
        $phone = $row["Telefono_numeris"];
        $role = $row["Role"];
		
		switch ($role)
        {
            case 1:
                $role = "Vadybininkas";
                break;
            case 2:
                $role = "Tiekėjas";
                break;
            case 3:
                $role = "Direktorius";
                break;
            default :
                $role = 'Neegzistuojanti rolė';
        }
		
        $userId = $row["id_Naudotojas"];
        $company = $row["fk_Tiekejo_imoneid_Tiekejo_imone"];
        
        if(strlen($company) != 0) {
            $q = "SELECT Pavadinimas FROM Tiekejo_imone WHERE id_Tiekejo_imone = '$company'";
            $r = $database->query($q);
            $r = mysqli_fetch_assoc($r);
            $company = $r["Pavadinimas"];
        }
		
		
        echo "<tr>";
		echo "<td>" . $i . "</td>";
		echo "<td>" . $name . "</td>";
		echo "<td>" . $sirname . "</td>";
		echo "<td>" . $email . "</td>";
		echo "<td>" . $phone . "</td>";
		echo "<td>" . $role . "</td>";
		echo "<td>" . $company . "</td>";
		echo '<td>
				<form action="updateRole.php" method="POST">
                    <input type="hidden" name="userId" value="' . $userId . '">
                    <select name="newRole" class="styled-select">
                        <option value="1">Vadybininkas</option>
                        <option value="2">Tiekėjas</option>
                        <option value="3">Direktorius</option>
                    </select>
                    <button type="submit" name="updateRole" class="customButton">Atnaujinti rolę</button>
                </form>
			  </td>';
		echo "</tr>\n";
    }
	echo "</table>";
}
?>

