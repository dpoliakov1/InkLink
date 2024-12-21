<?php
require_once '../constants.php';

$con = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME, DB_PORT) or die(mysqli_error($con));

if (!$con) {
    die("Не вдалося підключитися до бази даних: " . mysqli_connect_error());
}

function generateOtpCode() {
    return rand(100000, 999999);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name_reader = filter_var($_POST['name_reader'], FILTER_SANITIZE_STRING);
    $phone_num = filter_var($_POST['phone_num'], FILTER_SANITIZE_STRING);
    $birth_date = filter_var($_POST['birth_date'], FILTER_SANITIZE_STRING);
    $sub_id = filter_var($_POST['sub_id'], FILTER_VALIDATE_INT);

    if (!$sub_id) {
        echo "Помилка: Невірний ID підписки!";
        exit;
    }

    $otp_code = generateOtpCode();

    $query = "INSERT INTO readers (name_reader, phone_num, birth_date, otp_code, sub_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "ssssi", $name_reader, $phone_num, $birth_date, $otp_code, $sub_id);

    if (mysqli_stmt_execute($stmt)) {
        echo "Читач успішно доданий! Згенерований OTP код: $otp_code";
    } else {
        echo "Помилка додавання читача: " . mysqli_error($con);
    }

    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Додавання читача</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
        }

        h1 {
            text-align: center;
            margin-top: 30px;
            color: #333;
        }

        .form-container {
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            margin-top: 20px;
        }

        .form-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .form-container input[type="submit"] {
            background-color: #007BFF;
            color: white;
            cursor: pointer;
        }

        .form-container input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <h1>Додавання нового читача</h1>

    <div class="form-container">
        <form action="" method="POST">
            <label for="name_reader">Ім'я та прізвище:</label>
            <input type="text" id="name_reader" name="name_reader" required>

            <label for="phone_num">Телефон:</label>
            <input type="text" id="phone_num" name="phone_num" required>

            <label for="birth_date">Дата народження:</label>
            <input type="date" id="birth_date" name="birth_date" required>

            <label for="sub_id">ID підписки:</label>
            <input type="number" id="sub_id" name="sub_id" required>

            <input type="submit" value="Додати читача">
        </form>
    </div>

</body>
</html>
