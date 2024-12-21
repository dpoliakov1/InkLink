<?php
require_once '../constants.php';

function connectToDatabase() {
    $con = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    if (!$con) {
        die("Не вдалося підключитися до бази даних: " . mysqli_connect_error());
    }
    return $con;
}

function addSubscription($con, $sub_type, $start_date, $end_date, $status, $sub_price) {
    $sub_type = filter_var($sub_type, FILTER_SANITIZE_STRING);
    $start_date = filter_var($start_date, FILTER_SANITIZE_STRING);
    $end_date = filter_var($end_date, FILTER_SANITIZE_STRING);
    $sub_price = filter_var($sub_price, FILTER_VALIDATE_FLOAT);

    if (!$sub_price || $sub_price <= 0) {
        return "Помилка: Некоректна ціна підписки!";
    }

    $query = "INSERT INTO subscriptions (sub_type, start_date, end_date, status, sub_price) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "sssdi", $sub_type, $start_date, $end_date, $status, $sub_price);

    if (mysqli_stmt_execute($stmt)) {
        return "Підписка успішно додана!";
    } else {
        return "Помилка при додаванні підписки: " . mysqli_error($con);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $con = connectToDatabase();

    $sub_type = $_POST['sub_type'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = isset($_POST['status']) ? 1 : 0;
    $sub_price = $_POST['sub_price'];

    $message = addSubscription($con, $sub_type, $start_date, $end_date, $status, $sub_price);
    echo "<script>alert('$message');</script>";

    mysqli_close($con);
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Додати підписку</title>
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

        input[type="text"], input[type="number"], input[type="date"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus, input[type="number"]:focus, input[type="date"]:focus {
            border-color: #4CAF50;
        }

        input[type="checkbox"] {
            margin-right: 8px;
            vertical-align: middle;
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

    <h1>Додати нову підписку</h1>

    <div class="form-container">
        <form method="post" action="">
            <div class="form-group">
                <label for="sub_type">Тип підписки:</label>
                <input type="text" id="sub_type" name="sub_type" required>
            </div>

            <div class="form-group">
                <label for="start_date">Дата початку:</label>
                <input type="date" id="start_date" name="start_date" required>
            </div>

            <div class="form-group">
                <label for="end_date">Дата закінчення:</label>
                <input type="date" id="end_date" name="end_date" required>
            </div>

            <div class="form-group">
                <label for="status">Статус:</label>
                <input type="checkbox" id="status" name="status" value="1"> Активна
            </div>

            <div class="form-group">
                <label for="sub_price">Ціна підписки:</label>
                <input type="number" step="0.01" id="sub_price" name="sub_price" required>
            </div>

            <button type="submit">Додати підписку</button>
        </form>
    </div>

</body>
</html>
