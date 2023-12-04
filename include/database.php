<?php

include("constants.php");
include("PartItem.php");

class MySQLDB {

    var $connection;

    /* Class constructor */

    function MySQLDB() {
        /* Make connection to database */
        $this->connection = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME)
                or die(mysqli_error() . '<br><h1>Faile include/constants.php suveskite savo MySQLDB duomenis.</h1>');
    }

    function usernameTaken($username) {
        if (!get_magic_quotes_gpc()) {
            $username = addslashes($username);
        }
        $q = "SELECT El_pastas FROM Naudotojas WHERE El_pastas = '$username'";
        $result = mysqli_query($this->connection, $q);
        return (mysqli_num_rows($result) > 0);
    }


  
    function addNewUser($name, $sirname, $email, $phoneNumber, $password) {
		
        $q = "INSERT INTO Naudotojas (Vardas, Pavarde, El_pastas, Telefono_numeris, Slaptazodis, Registravimo_data, Role, id_Naudotojas, fk_Tiekejo_imoneid_Tiekejo_imone) 
      			VALUES ('$name', '$sirname', '$email', '$phoneNumber', '$password', NOW(), '1', NULL, NULL)";
		return mysqli_query($this->connection, $q);
    }

    function getUserInfo($username) {
        $q = "SELECT * FROM Naudotojas WHERE El_pastas = '$username'";
        $result = mysqli_query($this->connection, $q);
        /* Error occurred, return given name by default */
        if (!$result || (mysqli_num_rows($result) < 1)) {
            return NULL;
        }
        /* Return result array */
        $dbarray = mysqli_fetch_array($result);
        return $dbarray;
    }
    
    function checkUserCompany($username) {
        $q = "SELECT fk_Tiekejo_imoneid_Tiekejo_imone FROM Naudotojas WHERE El_pastas = '$username'";
        $result = mysqli_query($this->connection, $q);

        if (!$result || mysqli_num_rows($result) < 1) {
            return false; // No rows found, so return false.
        }

        $row = mysqli_fetch_assoc($result);
        $value = $row['fk_Tiekejo_imoneid_Tiekejo_imone'];

        if ($value === null) {
            return true; // Value is NULL.
        } else {
            return false; // Value is not NULL.
        }
    }
    
    function addNewCompany($subname, $subaddress, $subcode, $username) {
        $q = "INSERT INTO Tiekejo_imone (Pavadinimas, Adresas, Imones_kodas) VALUES ('$subname', '$subaddress', '$subcode')";
		mysqli_query($this->connection, $q);
        
        $q = "SELECT id_Tiekejo_imone FROM Tiekejo_imone WHERE Imones_kodas = '$subcode'";
        $result = mysqli_query($this->connection, $q);
        $row = mysqli_fetch_assoc($result);
        $value = $row['id_Tiekejo_imone'];
        
        $q = "UPDATE Naudotojas SET fk_Tiekejo_imoneid_Tiekejo_imone = '$value' WHERE El_pastas = '$username'";
		return mysqli_query($this->connection, $q);
        
    }
    
    function addPart($name, $manufacturer, $model, $price, $delivery, $amount, $username) {
       
        $q = "SELECT * FROM Naudotojas WHERE El_pastas = '$username'";
        $result = mysqli_query($this->connection, $q);
        $dbarray = mysqli_fetch_array($result);
        $companyId = $dbarray['fk_Tiekejo_imoneid_Tiekejo_imone'];
        
        $q = "INSERT INTO Lektuvo_dalis (Pavadinimas, Gamintojas, Modelis, Kaina, Pristatymo_laikas, Kiekis, id_Lektuvo_dalis, fk_Tiekejo_imoneid_Tiekejo_imone) 
            VALUES ('$name', '$manufacturer', '$model', '$price', '$delivery', '$amount', NULL, '$companyId')";
        
		return mysqli_query($this->connection, $q);
        
    }
	
	function confirmUserPass($username, $password) {
        if (!get_magic_quotes_gpc()) {
            $username = addslashes($username);
        }

        /* Verify that user is in database */
        $q = "SELECT Slaptazodis FROM Naudotojas WHERE El_pastas = '$username'";
        $result = mysqli_query($this->connection, $q);
        if (!$result || (mysqli_num_rows($result) < 1)) {
            return 1; //Indicates username failure
        }

        /* Retrieve password from result, strip slashes */
        $dbarray = mysqli_fetch_array($result);
        $dbarray['Slaptazodis'] = stripslashes($dbarray['Slaptazodis']);
        $password = stripslashes($password);

        /* Validate that password is correct */
        if ($password === $dbarray['Slaptazodis']) {
            return 0; //Success! Username and password confirmed
        } else {
            return 2; //Indicates password failure
        }
    }
	
	function confirmUserID($username, $userid) {
        /* Add slashes if necessary (for query) */
        if (!get_magic_quotes_gpc()) {
            $username = addslashes($username);
        }

        /* Verify that user is in database */
        $q = "SELECT id_Naudotojas FROM Naudotojas WHERE El_pastas = '$username'";
        $result = mysqli_query($this->connection, $q);
        if (!$result || (mysqli_num_rows($result) < 1)) {
            return 1; //Indicates username failure
        }

        /* Retrieve userid from result, strip slashes */
        $dbarray = mysqli_fetch_array($result);
        $dbarray['id_Naudotojas'] = stripslashes($dbarray['id_Naudotojas']);
        $userid = stripslashes($userid);

        /* Validate that userid is correct */
        if ($userid == $dbarray['id_Naudotojas']) {
            return 0; //Success! Username and userid confirmed
        } else {
            return 2; //Indicates userid invalid
        }
    }



    /**
     * query - Performs the given query on the database and
     * returns the result, which may be false, true or a
     * resource identifier.
     */
    function query($query) {
        return mysqli_query($this->connection, $query);
    }

}

/* Create database connection */
$database = new MySQLDB;

