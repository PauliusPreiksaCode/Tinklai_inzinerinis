<?php
include("include/session.php");
if ($session->logged_in) {
    header("Location: index.php");
} else {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=9; text/html; charset=utf-8"/>
        <title>Registracija</title>
        <link href="include/styles.css" rel="stylesheet" type="text/css"/>
        <style>
            .center {
                text-align: center;
            }
        </style>
    </head>
    <body>
        <?php  include("include/header.php"); ?>
    
    <?php
    if (isset($_SESSION['regsuccess'])) {
        if ($_SESSION['regsuccess']) {
            echo "<div class='center'><p>Ačiū, <b>" . $_SESSION['reguname'] . "</b>, Jūsų registracija atlikta sėkmingai, prašome prisijungti.</p></div>";
            echo '<div style="text-align: center;"><a href="index.php" class="customButton" style="text-decoration: none;">Prisijungti</a></div>';

            
        } else {
            echo "<div class='center'><p>Atsiprašome, bet vartotojo <b>" . $_SESSION['reguname'] . "</b>, registracija nebuvo sėkmingai baigta. Bandykite vėliau.</p></div>";
        }
        unset($_SESSION['regsuccess']);
        unset($_SESSION['reguname']);
    } else {
        ?>
        <div class="center">
            <?php
            if ($form->num_errors > 0) {
                echo "<p><span style='color: #ff0000;'>Klaidų: " . $form->num_errors . "</span></p>";
            }
            ?>
            <form action="process.php" method="POST" class="login-form">
                <h2 class="login-heading">Registracija</h2>
                <div class="form-group">
                    <label for="name">Vartotojo vardas:</label>
                    <input class="s1" name="name" type="text" size="15" value="<?php echo $form->value("name"); ?>">
                    <div class="error"><?php echo $form->error("name"); ?></div>
                </div>
                <div class="form-group">
                    <label for="sirname">Vartotojo pavardė:</label>
                    <input class="s1" name="sirname" type="text" size="15" value="<?php echo $form->value("sirname"); ?>">
                    <div class="error"><?php echo $form->error("sirname"); ?></div>
                </div>
                <div class="form-group">
                    <label for="email">E-paštas:</label>
                    <input class="s1" name="email" type="text" size="15" value="<?php echo $form->value("email"); ?>">
                    <div class="error"><?php echo $form->error("email"); ?></div>
                </div>
                <div class="form-group">
                    <label for="phone">Telefono numeris:</label>
                    <input class="s1" name="phone" type="text" size="15" value="<?php echo $form->value("phone"); ?>">
                    <div class="error"><?php echo $form->error("phone"); ?></div>
                </div>
                <div class="form-group">
                    <label for="pass">Slaptažodis:</label>
                    <input class="s1" name="pass" type="password" size="15" value="<?php echo $form->value("pass"); ?>">
                    <div class="error"><?php echo $form->error("pass"); ?></div>
                </div>
                <input type="hidden" name="subjoin" value="1">
                <div class="form-group">
                    <input type="submit" value="Registruotis">
                </div>
                <div class="center">
					<div class="backLink">
                    	<a href="index.php" class="customButton" style="text-decoration: none;">Atgal į Pradžia</a>
					</div>
                </div>
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
