<?php

include("include/session.php");

class Process {
    /* Class constructor */

    function Process() {
        global $session;
        /* User submitted login form */
        if (isset($_POST['sublogin'])) {
            $this->procLogin();
        }
        else if (isset($_POST['subjoin'])) {
            $this->procRegister();
        }
        else if (isset($_POST['subjoinCompany'])) {
            $this->procJoinCompany();
        }
        else if (isset($_POST['subAddPart'])) {
            $this->procAddPart();
        }
        /**
         * The only other reason user should be directed here
         * is if he wants to logout, which means user is
         * logged in currently.
         */ else if ($session->logged_in) {
            $this->procLogout();
        }
        /**
         * Should not get here, which means user is viewing this page
         * by mistake and therefore is redirected.
         */ else {
            header("Location: index.php");
        }
    }

    /**
     * procLogin - Processes the user submitted login form, if errors
     * are found, the user is redirected to correct the information,
     * if not, the user is effectively logged in to the system.
     */
    function procLogin() {
        global $session, $form;
        /* Login attempt */
        $retval = $session->login($_POST['email'], $_POST['pass'], isset($_POST['remember']));

        /* Login successful */
        if ($retval) {
            $session->logged_in = 1;
            header("Location: " . $session->referrer);
        }
        /* Login failed */ else {
            $session->logged_in = null;
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            header("Location: " . $session->referrer);
        }
    }

    /**
     * procLogout - Simply attempts to log the user out of the system
     * given that there is no logout form to process.
     */
    function procLogout() {
        global $session;
        $retval = $session->logout();
        header("Location: index.php");
    }
 

    function procRegister() {
        global $session, $form;

        /* Registration attempt */
        $retval = $session->register($_POST['name'], $_POST['sirname'], $_POST['email'], $_POST['phone'], $_POST['pass']);
        
        /* Registration Successful */
        if ($retval == 0) {
            $_SESSION['reguname'] = $_POST['email'];
            $_SESSION['regsuccess'] = true;
            header("Location: " . $session->referrer);
        }
        /* Error found with form */ else if ($retval == 1) {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            header("Location: " . $session->referrer);
        }
        /* Registration attempt failed */ else if ($retval == 2) {
            $_SESSION['reguname'] = $_POST['email'];
            $_SESSION['regsuccess'] = false;
            header("Location: " . $session->referrer);
        }
    }
    
    function procJoinCompany() {
        global $session, $form;

        /* Registration attempt */
        $retval = $session->joinCompanyCreate($_POST['name'], $_POST['address'], $_POST['code']);
        
        /* Registration Successful */
        if ($retval == 0) {
            $_SESSION['regCompanyname'] = $_POST['name'];
            $_SESSION['regCompanysuccess'] = true;
            header("Location: index.php");
        }
        /* Error found with form */ else if ($retval == 1) {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            header("Location: " . $session->referrer);
        }
        /* Registration attempt failed */ else if ($retval == 2) {
            $_SESSION['regCompanyname'] = $_POST['name'];
            $_SESSION['regCompanysuccess'] = false;
            header("Location: " . $session->referrer);
        }
    }
    
    function procAddPart() {
        global $session, $form;

        /* Registration attempt */
        $retval = $session->addPart($_POST['name'], $_POST['manufacturer'], $_POST['model'], $_POST['price'], $_POST['delivery'], $_POST['amount']);
        
        /* Registration Successful */
        if ($retval == 0) {
            $_SESSION['regPartname'] = $_POST['name'];
            $_SESSION['regPartsuccess'] = true;
            $_SESSION['delete_message'] = "Dalis sukurta sėkmingai";
            header("Location: index.php");
        }
        /* Error found with form */ else if ($retval == 1) {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            header("Location: " . $session->referrer);
        }
        /* Registration attempt failed */ else if ($retval == 2) {
            $_SESSION['regPartname'] = $_POST['name'];
            $_SESSION['regPartsuccess'] = false;
            $_SESSION['delete_message'] = "Dalis nebuvo sukurta sėkmingai";
            header("Location: " . $session->referrer);
        }
    }

  
}

/* Initialize process */
$process = new Process;