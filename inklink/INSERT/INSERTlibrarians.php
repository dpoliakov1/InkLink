<?php
require_once '../constants.php';

function connectToDatabase() {
    $con = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    if (!$con) {
        die("Не вдалося підключитися до бази даних: " . mysqli_connect_error());
    }
    return $con;
}

function isEmailUnique($con, $email) {
    $query = "SELECT COUNT(*) as count FROM librarians WHERE email = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row['count'] == 0;
}

function addLibrarian($con, $email, $libr_name, $password) {
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $libr_name = filter_var($libr_name, FILTER_SANITIZE_STRING);
    $password = password_hash($password, PASSWORD_BCRYPT);

    if (!isEmailUnique($con, $email)) {
        return "Бібліотекар з такою електронною поштою вже існує!";
    }

    $query = "INSERT INTO librarians (email, libr_name, password) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "sss", $email, $libr_name, $password);

    if (mysqli_stmt_execute($stmt)) {
        return "Бібліотекар успішно доданий!";
    } else {
        return "Помилка при додаванні бібліотекаря: " . mysqli_error($con);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $con = connectToDatabase();

    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $libr_name = filter_var($_POST['libr_name'], FILTER_SANITIZE_STRING);
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Некоректний формат електронної пошти!');</script>";
        exit;
    }

    if (strlen($password) < 6) {
        echo "<script>alert('Пароль має містити не менше 6 символів!');</script>";
        exit;
    }

    $message = addLibrarian($con, $email, $libr_name, $password);
    echo "<script>alert('$message');</script>";

    mysqli_close($con);
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Додати нового бібліотекаря</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .form-container {
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 500px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
        }

        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus {
            border-color: #4CAF50;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <h1>Додати нового бібліотекаря</h1>

    <div class="form-container">
        <form method="post" action="">
            <div class="form-group">
                <label for="email">Електронна пошта:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="libr_name">Ім'я бібліотекаря:</label>
                <input type="text" id="libr_name" name="libr_name" required>
            </div>

            <div class="form-group">
                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit">Додати бібліотекаря</button>
        </form>
    </div>

</body>
</html>
