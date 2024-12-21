<?php include("includes/header.php"); ?>
<div class="container mregister">
    <div id="login">
        <h1>Реєстрація бібліотекаря</h1>
        <form action="register.php" id="registerform" method="post" name="registerform">
            <p><label for="full_name">Повне ім'я<br>
                <input class="input" id="full_name" name="full_name" size="32" type="text" value=""></label></p>
                
            <p><label for="email">E-mail<br>
                <input class="input" id="email" name="email" size="32" type="email" value=""></label></p>
                    
            <p><label for="password">Пароль<br>
                <input class="input" id="password" name="password" size="32" type="password" value=""></label></p>
                        
            <p class="submit">
                <input class="button" id="register" name="register" type="submit" value="Зареєструватися">
            </p>

            <p class="regtext">Вже зареєстровані? <a href="login.php">Введіть ім'я користувача</a>!</p>
        </form>
    </div>
</div>

<?php
if (isset($_POST["register"])) {
    if (!empty($_POST['full_name']) && !empty($_POST['email']) && !empty($_POST['password'])) {
        $full_name = htmlspecialchars($_POST['full_name']);
        $email = htmlspecialchars($_POST['email']);
        $password = htmlspecialchars($_POST['password']);

        $conn = mysqli_connect("localhost", "root", "", "ink_link");

        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        $query = mysqli_query($conn, "SELECT * FROM librarians WHERE email='".$email."'");
        $numrows = mysqli_num_rows($query);

        if ($numrows == 0) {
            $sql = "INSERT INTO librarians (libr_name, email, password) VALUES('$full_name', '$email', '$password')";
            $result = mysqli_query($conn, $sql);

            if ($result) {
                $message = "Account Successfully Created";
            } else {
                $message = "Failed to insert data information!";
            }
        } else {
            $message = "That email already exists! Please try another one!";
        }

        mysqli_close($conn);
    } else {
        $message = "All fields are required!";
    }
}
?>

<?php if (!empty($message)) { echo "<p class='error'>MESSAGE: " . $message . "</p>"; } ?>

<?php include("includes/footer.php"); ?>
