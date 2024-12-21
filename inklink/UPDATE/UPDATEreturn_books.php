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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['return_id'])) {
    $errors = [];
    $return_id = sanitizeInput($_POST['return_id']);
    $librarian_id = sanitizeInput($_POST['librarian_id']);
    $reader_id = sanitizeInput($_POST['reader_id']);
    $book_code = sanitizeInput($_POST['book_code']);
    $actual_return = sanitizeInput($_POST['actual_return']);

    $errors['librarian_id'] = validateInput($librarian_id, 'number');
    $errors['reader_id'] = validateInput($reader_id, 'number');
    $errors['book_code'] = validateInput($book_code, 'number');
    $errors['actual_return'] = validateInput($actual_return, 'text');

    $errors = array_filter($errors);

    if (empty($errors)) {
        $update_query = "UPDATE return_books 
                         SET librarian_id = '$librarian_id', 
                             reader_id = '$reader_id', 
                             book_code = '$book_code', 
                             actual_return = '$actual_return' 
                         WHERE return_id = '$return_id'";

        if (mysqli_query($con, $update_query)) {
            echo "<p style='color: green;'>Дані повернення успішно оновлено!</p>";
        } else {
            echo "<p style='color: red;'>Помилка оновлення даних: " . mysqli_error($con) . "</p>";
        }
    } else {
        foreach ($errors as $field => $error) {
            echo "<p style='color: red;'>Помилка в полі '$field': $error</p>";
        }
    }
}

function getReturnBooks() {
    global $con;
    $query = "SELECT return_id, librarian_id, reader_id, book_code, actual_return FROM return_books";
    return mysqli_query($con, $query);
}

$return_books_result = getReturnBooks();
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оновлення запису про повернення книг</title>
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
        input[type="text"], input[type="number"], input[type="date"] {
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
    <h1>Оновлення запису про повернення книг</h1>

    <h2>Список повернень:</h2>
    <table>
        <tr>
            <th>ID повернення</th>
            <th>ID бібліотекаря</th>
            <th>ID читача</th>
            <th>Код книги</th>
            <th>Дата повернення</th>
            <th>Редагувати</th>
        </tr>
        <?php while ($return = mysqli_fetch_assoc($return_books_result)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($return['return_id']); ?></td>
                <td><?php echo htmlspecialchars($return['librarian_id']); ?></td>
                <td><?php echo htmlspecialchars($return['reader_id']); ?></td>
                <td><?php echo htmlspecialchars($return['book_code']); ?></td>
                <td><?php echo htmlspecialchars($return['actual_return']); ?></td>
                <td><a href="UPDATEreturn_books.php?return_id=<?php echo $return['return_id']; ?>">Редагувати</a></td>
            </tr>
        <?php } ?>
    </table>

    <?php
    if (isset($_GET['return_id'])) {
        $return_id = sanitizeInput($_GET['return_id']);
        $result = mysqli_query($con, "SELECT * FROM return_books WHERE return_id = '$return_id'");
        $return = mysqli_fetch_assoc($result);
    ?>

    <h2>Редагувати запис про повернення #<?php echo htmlspecialchars($return['return_id']); ?></h2>
    <form method="POST">
        <input type="hidden" name="return_id" value="<?php echo htmlspecialchars($return['return_id']); ?>">
        <label for="librarian_id">ID бібліотекаря:</label>
        <input type="number" name="librarian_id" value="<?php echo htmlspecialchars($return['librarian_id']); ?>" required>
        <label for="reader_id">ID читача:</label>
        <input type="number" name="reader_id" value="<?php echo htmlspecialchars($return['reader_id']); ?>" required>
        <label for="book_code">Код книги:</label>
        <input type="number" name="book_code" value="<?php echo htmlspecialchars($return['book_code']); ?>" required>
        <label for="actual_return">Дата фактичного повернення:</label>
        <input type="date" name="actual_return" value="<?php echo htmlspecialchars($return['actual_return']); ?>" required>
        <button type="submit">Зберегти зміни</button>
    </form>

    <?php
    }
    ?>

</body>
</html>
