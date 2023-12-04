<?php
if (isset($session) && $session->logged_in) {
    $path = "";
    if (isset($_SESSION['path'])) {
        $path = $_SESSION['path'];
        unset($_SESSION['path']);
    }
    ?>
    <div class="user-info">
        <p>Prisijungęs vartotojas: <span class="username"><?php echo $session->username; ?></span></p>
        <p>Naudotojo rolė: <span class="user-role">
            <?php 
                if($session->role == 1) echo "Vadybininkas"; 
                if($session->role == 2) echo "Tiekėjas"; 
                if($session->role == 3) echo "Direktorius"; 
            ?>
            </span></p>
        <a href="<?php echo $path . 'process.php'; ?>" class="logout-button">Atsijungti</a>
    </div>
    <?php
}
?>
