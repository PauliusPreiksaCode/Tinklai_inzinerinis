<?php
include("include/session.php");
if (!$session->logged_in) {
    header("Location: index.php");
} else {
    ?>
    <html>
        <head>  
            <meta http-equiv="X-UA-Compatible" content="IE=9; text/html; charset=utf-8"/> 
            <title>Pridėti kompaniją</title>
            <link href="include/styles.css" rel="stylesheet" type="text/css" />
        </head>
        <body>   
            <?php  
                include("include/header.php"); 
                include("include/meniu.php");
            ?>
            
                <?php
                        /**
                         * The user has submitted the registration form and the
                         * results have been processed.
                         */ if (isset($_SESSION['regCompanysuccess'])) {
                            /* Registracija sėkminga */
                            if ($_SESSION['regCompanysuccess']) {
                                unset($_SESSION['regCompanysuccess']);
                                unset($_SESSION['regCompanyname']);
                                //header("Location: index.php");
                            }
                            /* Registracija nesėkminga */ else {
                                echo "<p>Atsiprašome, bet kompanijos <b>" . $_SESSION['regCompanyname'] . "</b>, "
                                . " registracija nebuvo sėkmingai baigta.<br>Bandykite vėliau.</p>";
                            }
                            unset($_SESSION['regCompanysuccess']);
                            unset($_SESSION['regCompanyname']);
                        }
                        /**
                         * The user has not filled out the registration form yet.
                         * Below is the page with the sign-up form, the names
                         * of the input fields are important and should not
                         * be changed.
                         */ else {
                            ?>
                <h1 style="text-align: center;">Pasirinkite įmonę arba pridėkite naują</h1>
                <?php
                    include("include/selectCompany.php");
                             ?>
                
                            <div align="center">
                                <?php
                                if ($form->num_errors > 0) {
                                    echo "<font size=\"3\" color=\"#ff0000\">Klaidų: " . $form->num_errors . "</font>";
                                }
                                ?>                            
                                            <form action="process.php" method="POST" class="styled-form">
                                                <center style="font-size: 18pt;"><label><b>Pridėti kompaniją</b></label></center>
                                                <p>
                                                    <label for="name">Pavadinimas:</label>
                                                    <input class="s1" name="name" id="name" type="text" size="15" value="<?php echo $form->value("name"); ?>">
                                                </p>
                                                <p>
                                                    <label for="address">Adresas:</label>
                                                    <input class="s1" name="address" id="address" type="text" size="15" value="<?php echo $form->value("address"); ?>">
                                                </p>
                                                <p>
                                                    <label for="code">Įmonės kodas:</label>
                                                    <input class="s1" name="code" id="code" type="text" size="15" value="<?php echo $form->value("code"); ?>">
                                                </p>
                                                <p>
                                                    <input type="hidden" name="subjoinCompany" value="1">
                                                    <input type="submit" value="Pridėti" style="margin-top: 10px; padding: 10px 20px; background-color: #007bff; color: #fff; border: none; border-radius: 5px; cursor: pointer;">
                                                </p>
                                            </form>
                            </div>
                        <?php
                        }
                        ?>
        </body>
    </html>
    <?php
}
?>

