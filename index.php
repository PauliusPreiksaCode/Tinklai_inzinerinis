<?php
include("include/session.php");

global $database;
?>
<html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=9; text/html; charset=utf-8"/>
        <title>Inzinerinis projektas</title>
        <link href="include/styles.css" rel="stylesheet" type="text/css" />
    </head>
    <body>             
        <?php  include("include/header.php"); ?>
        
            <?php
            
            //Jei vartotojas prisijungęs
            if ($session->logged_in) {
				include("include/meniu.php");
				
				// Jei admin
				if($session->role == 3)
				{
					include("include/userPanel.php");
                    include("include/statistics.php");
				}
                // Jei tiekejas
                else if($session->role == 2)
                {
                    if($database->checkUserCompany($session->username)) {
                       header("Location: createCompany.php");
                    }
                    else {
                        include("include/showSellingParts.php");
                    }
                    
                }
                else if($session->role == 1)
                {
                    include("include/orderView.php");
                }
                
                
            } else {
                echo "<div align=\"center\">";
                if ($form->num_errors > 0) {
                    echo "<font size=\"3\" color=\"#ff0000\">Klaidų: " . $form->num_errors . "</font>";
                }
                include("include/loginForm.php");
            }
            ?>
</body>
</html>