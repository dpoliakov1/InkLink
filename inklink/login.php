<?php
session_start();
require_once("includes/connection.php");

if (isset($_SESSION["session_username"])) {
    header("Location: intropage.php");
    exit();
}

if (isset($_POST["login"])) {
    if (!empty($_POST['full_name']) && !empty($_POST['password'])) {
        $full_name = htmlspecialchars($_POST['full_name']);
        $password = htmlspecialchars($_POST['password']);

        $conn = mysqli_connect("localhost", "root", "", "ink_link");

        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        $query = mysqli_query($conn, "SELECT * FROM librarians WHERE libr_name='" . $full_name . "' AND password='" . $password . "'");
        $numrows = mysqli_num_rows($query);

        if ($numrows != 0) {
            while ($row = mysqli_fetch_assoc($query)) {
                $dbusername = $row['libr_name'];
                $dbpassword = $row['password'];
            }

            if ($full_name == $dbusername && $password == $dbpassword) {
                $_SESSION['session_username'] = $full_name;
                header("Location: intropage.php");
                exit();
            } else {
                $message = "Невірне ім'я користувача або пароль!";
            }
        } else {
            $message = "Невірне ім'я користувача або пароль!";
        }

        mysqli_close($conn);
    } else {
        $message = "Будь ласка, заповніть всі поля!";
    }
}
?>

<?php include("includes/header.php"); ?>

<div class="container mlogin">
    <div id="login">
        <h1>Вхід бібліотекаря</h1>
        <form action="login.php" id="loginform" method="post" name="loginform">
            <p><label for="full_name">Повне ім'я<br>
                <input class="input" id="full_name" name="full_name" size="20" type="text" value=""></label></p>

            <p><label for="password">Пароль<br>
                <input class="input" id="password" name="password" size="20" type="password" value=""></label></p>

            <p class="submit">
                <input class="button" name="login" type="submit" value="Увійти">
            </p>

            <p class="regtext">Ще не зареєстровані? <a href="register.php">Реєстрація</a>!</p>
        </form>
    </div>
</div>

<?php
if (isset($message)) {
    echo "<p class='error'>$message</p>";
}
?>

<?php include("includes/footer.php"); ?>
