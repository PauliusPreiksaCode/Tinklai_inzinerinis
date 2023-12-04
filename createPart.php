<?php
include("include/session.php");
if (!$session->logged_in) {
    header("Location: index.php");
} else {
    ?>
    <html>
        <head>  
            <meta http-equiv="X-UA-Compatible" content="IE=9; text/html; charset=utf-8"/> 
            <title>Sukurti dalį</title>
            <link href="include/styles.css" rel="stylesheet" type="text/css" />
        </head>
        <body>   
            <?php  
                include("include/header.php"); 
                include("include/meniu.php");
            ?>
                <div style="padding-top: 10px">
                    <a href="index.php" class="customButton" style="text-decoration: none;">Atgal</a>
                </div>
                            <div align="center">
                                <?php
                                if ($form->num_errors > 0) {
                                    echo "<font size=\"3\" color=\"#ff0000\">Klaidų: " . $form->num_errors . "</font>";
                                }
                                ?>                            
                                            <form action="process.php" method="POST" class="styled-form">              
                                                <center style="font-size:18pt;"><label><b>Pridėti dalį</b></label></center>
                                                <p>
                                                    <label for="name">Pavadinimas:</label>
                                                    <input class ="s1" name="name" type="text" size="15"
                                                           value="<?php echo $form->value("name"); ?>"/><br><?php echo $form->error("name"); ?>
                                                </p>
                                                <p>
                                                    <label for="manufacturer">Gamintojas:</label>
                                                    <input class ="s1" name="manufacturer" type="text" size="15"
                                                           value="<?php echo $form->value("manufacturer"); ?>"/><br><?php echo $form->error("manufacturer"); ?>
                                                </p>
                                                <p>
                                                    <label for="model">Modelis:</label>
                                                    <input class ="s1" name="model" type="text" size="15"
                                                           value="<?php echo $form->value("model"); ?>"/><br><?php echo $form->error("model"); ?>
                                                </p>
												 <p>
                                                     <label for="price">Kaina:</label>
                                                    <input class ="s1" name="price" type="text" size="15"
                                                           value="<?php echo $form->value("price"); ?>"/><br><?php echo $form->error("price"); ?>
                                                </p>
												<p>
                                                    <label for="delivery">Pristatymo laikas dienomis:</label>
                                                    <input class ="s1" name="delivery" type="text" size="15"
                                                           value="<?php echo $form->value("delivery"); ?>"/><br><?php echo $form->error("delivery"); ?>
                                                </p>
												<p>
                                                    <label for="amount">Kiekis:</label>
                                                    <input class ="s1" name="amount" type="text" size="15"
                                                           value="<?php echo $form->value("amount"); ?>"/><br><?php echo $form->error("amount"); ?>
                                                </p>
                                                <p>
                                                    <input type="hidden" name="subAddPart" value="1">
                                                    <input type="submit" value="Pridėti" style="margin-top: 10px; padding: 10px 20px; background-color: #007bff; color: #fff; border: none; border-radius: 5px; cursor: pointer;">
                                                </p>
                                            </form>
                            </div>
        </body>
    </html>
    <?php
}
?>


