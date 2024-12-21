<?php
require_once '../constants.php';

$con = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME, DB_PORT) or die(mysqli_error($con));

if (!$con) {
    die("Не вдалося підключитися до бази даних: " . mysqli_connect_error());
}

function sanitizeInput($input) {
    global $con;
    return htmlspecialchars(mysqli_real_escape_string($con, trim($input)));
}

function validateInput($input, $type) {
    if (empty($input)) {
        return "Поле не повинно бути порожнім.";
    }
    if ($type === 'number' && !is_numeric($input)) {
        return "Введене значення повинно бути числом.";
    }
    return null;
}

function getSubscriptions() {
    global $con;
    $query = "SELECT sub_id, sub_type, start_date, end_date, status, sub_price FROM subscriptions";
    return mysqli_query($con, $query);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['sub_id'])) {
    $errors = [];

    $sub_id = sanitizeInput($_POST['sub_id']);
    $sub_type = sanitizeInput($_POST['sub_type']);
    $start_date = sanitizeInput($_POST['start_date']);
    $end_date = sanitizeInput($_POST['end_date']);
    $status = sanitizeInput($_POST['status']);
    $sub_price = sanitizeInput($_POST['sub_price']);

    $errors['sub_type'] = validateInput($sub_type, 'text');
    $errors['start_date'] = validateInput($start_date, 'text');
    $errors['end_date'] = validateInput($end_date, 'text');
    $errors['status'] = validateInput($status, 'number');
    $errors['sub_price'] = validateInput($sub_price, 'number');

    $errors = array_filter($errors);

    if (empty($errors)) {
        $update_query = "UPDATE subscriptions 
                         SET sub_type = ?, 
                             start_date = ?, 
                             end_date = ?, 
                             status = ?, 
                             sub_price = ? 
                         WHERE sub_id = ?";
        $stmt = mysqli_prepare($con, $update_query);
        mysqli_stmt_bind_param($stmt, "sssidi", $sub_type, $start_date, $end_date, $status, $sub_price, $sub_id);

        if (mysqli_stmt_execute($stmt)) {
            echo "<p style='color: green;'>Дані підписки успішно оновлено!</p>";
        } else {
            echo "<p style='color: red;'>Помилка оновлення даних: " . mysqli_error($con) . "</p>";
        }
    } else {
        foreach ($errors as $field => $error) {
            echo "<p style='color: red;'>Помилка в полі '$field': $error</p>";
        }
    }
}

$subscriptions_result = getSubscriptions();
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оновлення підписок</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
            color: #333;
        }

        h1 {
            color: #5cb85c;
        }

        table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
            background-color: #fff;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            width: 300px;
            margin: 0 auto;
        }

        label {
            margin-bottom: 5px;
            display: block;
        }

        input[type="text"], input[type="number"], input[type="date"], select {
            width: 100%;
            padding: 8px;
            margin: 5px 0 15px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        button {
            padding: 10px 20px;
            background-color: #5cb85c;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #4cae4c;
        }
    </style>
</head>
<body>
    <h1>Оновлення підписок</h1>

    <h2>Список підписок:</h2>
    <table>
        <tr>
            <th>ID підписки</th>
            <th>Тип</th>
            <th>Дата початку</th>
            <th>Дата закінчення</th>
            <th>Статус</th>
            <th>Ціна</th>
            <th>Редагувати</th>
        </tr>
        <?php while ($subscription = mysqli_fetch_assoc($subscriptions_result)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($subscription['sub_id']); ?></td>
                <td><?php echo htmlspecialchars($subscription['sub_type']); ?></td>
                <td><?php echo htmlspecialchars($subscription['start_date']); ?></td>
                <td><?php echo htmlspecialchars($subscription['end_date']); ?></td>
                <td><?php echo $subscription['status'] == 1 ? 'Активна' : 'Неактивна'; ?></td>
                <td><?php echo htmlspecialchars($subscription['sub_price']); ?> грн</td>
                <td><a href="UPDATEsubscriptions.php?sub_id=<?php echo $subscription['sub_id']; ?>">Редагувати</a></td>
            </tr>
        <?php } ?>
    </table>

    <?php
    if (isset($_GET['sub_id'])) {
        $sub_id = sanitizeInput($_GET['sub_id']);
        $result = mysqli_query($con, "SELECT * FROM subscriptions WHERE sub_id = '$sub_id'");
        $subscription = mysqli_fetch_assoc($result);
    ?>

    <h2>Редагувати підписку #<?php echo htmlspecialchars($subscription['sub_id']); ?></h2>
    <form method="POST">
        <input type="hidden" name="sub_id" value="<?php echo htmlspecialchars($subscription['sub_id']); ?>">
        <label for="sub_type">Тип підписки:</label>
        <input type="text" name="sub_type" value="<?php echo htmlspecialchars($subscription['sub_type']); ?>" required><br>
        <label for="start_date">Дата початку:</label>
        <input type="date" name="start_date" value="<?php echo htmlspecialchars($subscription['start_date']); ?>" required><br>
        <label for="end_date">Дата закінчення:</label>
        <input type="date" name="end_date" value="<?php echo htmlspecialchars($subscription['end_date']); ?>" required><br>
        <label for="status">Статус:</label>
        <select name="status">
            <option value="1" <?php if ($subscription['status'] == 1) echo 'selected'; ?>>Активна</option>
            <option value="0" <?php if ($subscription['status'] == 0) echo 'selected'; ?>>Неактивна</option>
        </select><br>
        <label for="sub_price">Ціна:</label>
        <input type="number" step="0.01" name="sub_price" value="<?php echo htmlspecialchars($subscription['sub_price']); ?>" required><br>
        <button type="submit">Зберегти зміни</button>
    </form>

    <?php } ?>

</body>
</html>
