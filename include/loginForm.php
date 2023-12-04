<?php
if (isset($form) && isset($session) && !$session->logged_in) {
    ?>   
    <form action="process.php" method="POST" class="login-form">
        <h2 class="login-heading">Prisijungimas</h2>
        <div class="form-group">
            <label for="email">Vartotojo el. paštas:</label>
            <input class="s1" name="email" type="text" value="<?php echo $form->value("email"); ?>">
            <div class="error"><?php echo $form->error("email"); ?></div>
        </div>
        <div class="form-group">
            <label for="pass">Slaptažodis:</label>
            <input class="s1" name="pass" type="password" value="<?php echo $form->value("pass"); ?>">
            <div class="error"><?php echo $form->error("pass"); ?></div>
        </div>
        <div class="form-group">
            <input type="submit" value="Prisijungti">
            <input type="checkbox" name="remember" <?php echo $form->value("remember") != "" ? 'checked' : ''; ?>> Atsiminti
        </div>
        <input type="hidden" name="sublogin" value="1">
        <p class="register-link">
            <a href="register.php" class="customButton" style="text-decoration: none;">Registracija</a>
        </p>     
    </form>
    <?php
}
?>
