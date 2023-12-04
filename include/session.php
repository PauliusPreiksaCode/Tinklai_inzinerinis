<?php

include("database.php");
include("form.php");

class Session {

    var $username;     //Username given on sign-up
    var $userid;       //Random value generated on current login
    var $role;    //The level to which the user pertains
    var $logged_in;    //True if user is logged in, false otherwise
    var $userinfo = array();  //The array holding all user info
    var $url;          //The page url current being viewed
    var $referrer;     //Last recorded site page viewed

    /**
     * Note: referrer should really only be considered the actual
     * page referrer in process.php, any other time it may be
     * inaccurate.
     */
    /* Class constructor */

    function Session() {
        $this->time = time();
        $this->startSession();
    }

    /**
     * startSession - Performs all the actions necessary to 
     * initialize this session object. Tries to determine if the
     * the user has logged in already, and sets the variables 
     * accordingly. Also takes advantage of this page load to
     * update the active visitors tables.
     */
    function startSession() {
        global $database;  //The database connection
        session_start();   //Tell PHP to start the session

        /* Determine if user is logged in */
        $this->logged_in = $this->checkLogin();

        

        /* Set referrer page */
        if (isset($_SESSION['url'])) {
            $this->referrer = $_SESSION['url'];
        } else {
            $this->referrer = "/";
        }

        /* Set current url */
        $this->url = $_SESSION['url'] = $_SERVER['PHP_SELF'];
    }


    function checkLogin() {
        global $database;  //The database connection
        /* Check if user has been remembered */
        if (isset($_COOKIE['cookname']) && isset($_COOKIE['cookid'])) {
            $this->username = $_SESSION['username'] = $_COOKIE['cookname'];
            $this->userid = $_SESSION['userid'] = $_COOKIE['cookid'];
        }

        /* Username and userid have been set and not guest */
        if (isset($_SESSION['username']) && isset($_SESSION['userid'])) {
            /* Confirm that username and userid are valid */
            if ($database->confirmUserID($_SESSION['username'], $_SESSION['userid']) != 0) {
                /* Variables are incorrect, user not logged in */
                unset($_SESSION['username']);
                unset($_SESSION['userid']);
                return false;
            }

            /* User is logged in, set class variables */
            $this->userinfo = $database->getUserInfo($_SESSION['username']);
            $this->username = $this->userinfo['El_pastas'];
            $this->userid = $this->userinfo['id_Naudotojas'];
            $this->role = $this->userinfo['Role'];
            return true;
        }

        /* User not logged in */ else {
            return false;
        }
    }


    function login($subuser, $subpass, $subremember) {
        global $database, $form;  //The database and form object

        /* Username error checking */
        $field = "email";  //Use field name for username
        if (!$subuser || strlen($subuser = trim($subuser)) == 0) {
            $form->setError($field, "* Neįvestas vartotojo el paštas");
        } 

        /* Password error checking */
        $field = "pass";  //Use field name for password
        if (!$subpass) {
            $form->setError($field, "* Neįvestas slaptažodis");
        }

        /* Return if form errors exist */
        if ($form->num_errors > 0) {
            return false;
        }

        $subuser = stripslashes($subuser);
        $result = $database->confirmUserPass($subuser, md5($subpass));

        /* Check error codes */
        if ($result == 1) {
            $field = "email";
            $form->setError($field, "* Tokio vartotojo nėra");
        } else if ($result == 2) {
            $field = "pass";
            $form->setError($field, "* Neteisingas slaptažodis");
        }

        /* Return if form errors exist */
        if ($form->num_errors > 0) {
            return false;
        }

        /* Username and password correct, register session variables */
        $this->userinfo = $database->getUserInfo($subuser);
        $this->username = $_SESSION['username'] = $this->userinfo['El_pastas'];
        $this->userid = $_SESSION['userid'] = $this->userinfo['id_Naudotojas'];
        $this->role = $this->userinfo['Role'];

        if ($subremember) {
            setcookie("cookname", $this->username, time() + COOKIE_EXPIRE, COOKIE_PATH);
            setcookie("cookid", $this->userid, time() + COOKIE_EXPIRE, COOKIE_PATH);
        }

        return true;
    }

    function logout() {
        global $database;  //The database connection
		
        if (isset($_COOKIE['cookname']) && isset($_COOKIE['cookid'])) {
            setcookie("cookname", "", time() - COOKIE_EXPIRE, COOKIE_PATH);
            setcookie("cookid", "", time() - COOKIE_EXPIRE, COOKIE_PATH);
        }

        /* Unset PHP session variables */
        unset($_SESSION['username']);
        unset($_SESSION['userid']);

        /* Reflect fact that user has logged out */
        $this->logged_in = false;
    }

    /**
     * register - Gets called when the user has just submitted the
     * registration form. Determines if there were any errors with
     * the entry fields, if so, it records the errors and returns
     * 1. If no errors were found, it registers the new user and
     * returns 0. Returns 2 if registration failed.
     */
    function register($subname, $subsirname, $subemail, $subphone, $subpass) {
        global $database, $form;  //The database, form and mailer object

		
        /* Username error checking */
        $field = "email";  //Use field name for username
        if (!$subemail || strlen($subemail = trim($subemail)) == 0) {
            $form->setError($field, "* Vartotojas neįvestas");
        } else {
            /* Spruce up username, check length */
            $subemail = stripslashes($subemail);
            if (strlen($subemail) < 5) {
                $form->setError($field, "* Vartotojo vardas turi mažiau kaip 5 simbolius");
            } else if (strlen($subemail) > 50) {
                $form->setError($field, "* Vartotojo vardas virš 50 simbolių");
            }
            /* Check if username is already in use */ else if ($database->usernameTaken($subemail)) {
                $form->setError($field, "* Toks vartotojo vardas jau yra");
            }
        }

        /* Password error checking */
        $field = "pass";  //Use field name for password
        if (!$subpass) {
            $form->setError($field, "* Neįvestas slaptažodis");
        } else {
            /* Spruce up password and check length */
            $subpass = stripslashes($subpass);
            if (strlen($subpass) < 4) {
                $form->setError($field, "* Ne mažiau kaip 4 simboliai");
            }
            /* Check if password is not alphanumeric */ else if (!ctype_alnum($subpass = trim($subpass))) {
                $form->setError($field, "* Slaptažodis gali būti sudarytas
                    <br>&nbsp;&nbsp;tik iš raidžių ir skaičių");
            }
        }

        /* Email error checking */
        $field = "email";  //Use field name for email
        if (!$subemail || strlen($subemail = trim($subemail)) == 0) {
            $form->setError($field, "* Neįvestas e-pašto adresas");
        } else {
            /* Check if valid email address */
          //  $regex = "^[_+a-z0-9-]+(\.[_+a-z0-9-]+)*"
            //        . "@[a-z0-9-]+(\.[a-z0-9-]{1,})*"
            //        . "\.([a-z]{2,}){1}$";
           if (!(filter_var($subemail, FILTER_VALIDATE_EMAIL))) {
                $form->setError($field, "* Klaidingas e-pašto adresas");
            }
            $subemail = stripslashes($subemail);
        }

        /* Errors exist, have user correct them */
        if ($form->num_errors > 0) {
            return 1;  //Errors with form
        }
         else {
			 
            if ($database->addNewUser($subname, $subsirname, $subemail, $subphone, md5($subpass))) {
                return 0;  //New user added succesfully
            } else {
                return 2;  //Registration attempt failed
            }
        }
    }
    
    function joinCompanyCreate($subname, $subaddress, $subcode) {
        global $database, $form;  //The database, form and mailer object

        $field = "name";
        if (!$subname || strlen($subname = trim($subname)) == 0) {
            $form->setError($field, "* Pavadinimas neįvestas");
        } 


        $field = "address";
        if (!$subaddress || strlen($subaddress = trim($subaddress)) == 0) {
            $form->setError($field, "* Neįvestas adresas");
        } 


        $field = "code"; 
        if (!$subcode || strlen($subcode = trim($subcode)) == 0) {
            $form->setError($field, "* Neįvestas įmonės kodas");
        } 

        if ($form->num_errors > 0) {
            return 1;  //Errors with form
        }
         else 
         {
			 
            if ($database->addNewCompany($subname, $subaddress, $subcode, $_SESSION['username'])) {
                return 0; 
            } 
            else {
                return 2; 
            }   
        }
    }
    
    function addPart($subname, $submanufacturer, $submodel, $subprice, $subdelivery, $subamount){
        global $database, $form; 
        
        
        $field = "name";
        if (!$subname || strlen($subname = trim($subname)) == 0) {
            $form->setError($field, "* Pavadinimas neįvestas");
        } 
        
        $field = "manufacturer";
        if (!$submanufacturer || strlen($submanufacturer = trim($submanufacturer)) == 0) {
            $form->setError($field, "* Gamintojas neįvestas");
        } 
        
        $field = "model";
        if (!$submodel || strlen($submodel = trim($submodel)) == 0) {
            $form->setError($field, "* Modelis neįvestas");
        } 
        
        $field = "price";
        if (!$subprice || strlen($subprice = trim($subprice)) == 0) {
            $form->setError($field, "* Kaina neįvesta");
        } else if($subprice <= 0) {
            $form->setError($field, "* Kaina turi būti didesnė už nulį");   
        }
        
        $field = "delivery";
        if (!$subdelivery || strlen($subdelivery = trim($subdelivery)) == 0) {
            $form->setError($field, "* Pristatymo laikas neįvestas");
        } else if($subdelivery <= 0) {
            $form->setError($field, "* Pristatymo laikas turi būti didesnis už nulį");   
        }
        
        $field = "amount";
        if (!$subamount || strlen($subamount = trim($subamount)) == 0) {
            $form->setError($field, "* Kiekis neįvestas");
        } else if($subamount <= 0) {
            $form->setError($field, "* Kiekis turi būti didesnis už nulį");   
        }
        
        if ($form->num_errors > 0) {
            return 1;  //Errors with form
        }
         else 
         {
			 
            if ($database->addPart($subname, $submanufacturer, $submodel, $subprice, $subdelivery, $subamount, $_SESSION['username'])) {
                return 0; 
            } 
            else {
                return 2; 
            }   
        }
    }
}

$session = new Session;

/* Initialize form object */
$form = new Form;

