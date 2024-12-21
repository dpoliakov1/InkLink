<?php
session_start();

if (!isset($_SESSION["session_username"])) {
    header("location:login.php");
} else {
?>

<?php include("includes/header.php"); ?>

<div id="welcome">
    <h2>Ласкаво просимо, <span><?php echo $_SESSION['session_username']; ?>!</span></h2>
    <p><a href="logout.php">Вийти</a> з системи</p>
</div>

<?php include("includes/footer.php"); ?>

<?php } ?>