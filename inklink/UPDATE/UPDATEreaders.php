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

function validatePhoneNumber($phone) {
    return preg_match('/^\+?[0-9]{10,15}$/', $phone);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
    $reader_id = intval($_GET['id']);
    $name_reader = sanitizeInput($_POST['name_reader']);
    $phone_num = sanitizeInput($_POST['phone_num']);
    $birth_date = sanitizeInput($_POST['birth_date']);
    $sub_id = intval($_POST['sub_id']);

    $errors = [];

    if (empty($name_reader)) $errors[] = "Ім'я та прізвище не можуть бути порожніми.";
    if (!validatePhoneNumber($phone_num)) $errors[] = "Неправильний формат телефону.";
    if (empty($birth_date)) $errors[] = "Дата народження не може бути порожньою.";
    if ($sub_id <= 0) $errors[] = "ID підписки має бути позитивним числом.";

    if (empty($errors)) {
        $update_query = "UPDATE readers SET name_reader = ?, phone_num = ?, birth_date = ?, sub_id = ? WHERE reader_id = ?";
        $stmt = mysqli_prepare($con, $update_query);
        mysqli_stmt_bind_param($stmt, "sssii", $name_reader, $phone_num, $birth_date, $sub_id, $reader_id);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) > 0) {
            echo "<p class='success'>Дані успішно оновлено!</p>";
        } else {
            echo "<p class='error'>Помилка при оновленні даних.</p>";
        }
    } else {
        foreach ($errors as $error) {
            echo "<p class='error'>$error</p>";
        }
    }
}

function getReaders() {
    global $con;
    $query = "SELECT reader_id, name_reader, phone_num, birth_date, sub_id FROM readers";
    $result = mysqli_query($con, $query);

    if (!$result) {
        die("Помилка виконання запиту: " . mysqli_error($con));
    }

    $readers = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $readers[] = $row;
    }

    return $readers;
}

$reader = null;
if (isset($_GET['id'])) {
    $reader_id = intval($_GET['id']);
    $query = "SELECT * FROM readers WHERE reader_id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $reader_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $reader = mysqli_fetch_assoc($result);

    if (!$reader) {
        die("Читача не знайдено.");
    }
}

$readers = getReaders();
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Читачі бібліотеки</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        h1 {
            text-align: center;
            color: #333;
            padding: 20px;
            background-color: #007bff;
            color: white;
            margin-bottom: 30px;
        }
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .form-container label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }
        .form-container input {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-container button {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .form-container button:hover {
            background-color: #0056b3;
        }
        .success {
            color: green;
            font-weight: bold;
            text-align: center;
        }
        .error {
            color: red;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body>

<h1>Читачі бібліотеки</h1>

<?php if (isset($_GET['id']) && $reader): ?>
    <h2>Редагування читача ID: <?php echo htmlspecialchars($reader['reader_id']); ?></h2>
    <div class="form-container">
        <form method="post">
            <label>Ім'я та прізвище:
                <input type="text" name="name_reader" value="<?php echo htmlspecialchars($reader['name_reader']); ?>" required>
            </label>
            <label>Телефон:
                <input type="text" name="phone_num" value="<?php echo htmlspecialchars($reader['phone_num']); ?>" required>
            </label>
            <label>Дата народження:
                <input type="date" name="birth_date" value="<?php echo htmlspecialchars($reader['birth_date']); ?>" required>
            </label>
            <label>Підписка ID:
                <input type="number" name="sub_id" value="<?php echo htmlspecialchars($reader['sub_id']); ?>" required>
            </label>
            <button type="submit">Зберегти зміни</button>
        </form>
    </div>
    <p><a href="?">Повернутися до списку читачів</a></p>

<?php else: ?>
    <?php if ($readers): ?>
        <div>
            <?php foreach ($readers as $reader): ?>
                <div>
                    <h3>Читач ID: <?php echo htmlspecialchars($reader['reader_id']); ?></h3>
                    <p><strong>Ім'я та прізвище:</strong> <?php echo htmlspecialchars($reader['name_reader']); ?></p>
                    <p><strong>Телефон:</strong> <?php echo htmlspecialchars($reader['phone_num']); ?></p>
                    <p><strong>Дата народження:</strong> <?php echo htmlspecialchars($reader['birth_date']); ?></p>
                    <p><strong>Підписка ID:</strong> <?php echo htmlspecialchars($reader['sub_id']); ?></p>
                    <p><a href="?id=<?php echo urlencode($reader['reader_id']); ?>">Редагувати</a></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Читачів не знайдено.</p>
    <?php endif; ?>
<?php endif; ?>

</body>
</html>
