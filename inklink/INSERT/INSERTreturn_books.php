<?php
require_once '../constants.php';

function connectToDatabase() {
    $con = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    if (!$con) {
        die("Не вдалося підключитися до бази даних: " . mysqli_connect_error());
    }
    return $con;
}

function isValidLibrarianId($con, $librarian_id) {
    $query = "SELECT COUNT(*) as count FROM librarians WHERE librarian_id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $librarian_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row['count'] > 0;
}

function isValidReaderId($con, $reader_id) {
    $query = "SELECT COUNT(*) as count FROM readers WHERE reader_id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $reader_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row['count'] > 0;
}

function isValidBookCode($con, $book_code) {
    $query = "SELECT COUNT(*) as count FROM books WHERE book_code = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "s", $book_code);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row['count'] > 0;
}

function addReturnBook($con, $librarian_id, $reader_id, $book_code, $actual_return) {
    $librarian_id = filter_var($librarian_id, FILTER_VALIDATE_INT);
    $reader_id = filter_var($reader_id, FILTER_VALIDATE_INT);
    $book_code = filter_var($book_code, FILTER_SANITIZE_STRING);
    $actual_return = filter_var($actual_return, FILTER_SANITIZE_STRING);

    if (!$librarian_id || !isValidLibrarianId($con, $librarian_id)) {
        return "Помилка: Невірний ID бібліотекаря!";
    }

    if (!$reader_id || !isValidReaderId($con, $reader_id)) {
        return "Помилка: Невірний ID читача!";
    }

    if (!isValidBookCode($con, $book_code)) {
        return "Помилка: Невірний код книги!";
    }

    $query = "INSERT INTO return_books (librarian_id, reader_id, book_code, actual_return) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "iiss", $librarian_id, $reader_id, $book_code, $actual_return);

    if (mysqli_stmt_execute($stmt)) {
        return "Запис успішно доданий у таблицю повернень!";
    } else {
        return "Помилка при додаванні запису: " . mysqli_error($con);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $con = connectToDatabase();

    $librarian_id = $_POST['librarian_id'];
    $reader_id = $_POST['reader_id'];
    $book_code = $_POST['book_code'];
    $actual_return = $_POST['actual_return'];

    $message = addReturnBook($con, $librarian_id, $reader_id, $book_code, $actual_return);
    echo "<script>alert('$message');</script>";

    mysqli_close($con);
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Додати повернення книги</title>
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

    <h1>Додати запис про повернення книги</h1>

    <div class="form-container">
        <form method="post" action="">
            <div class="form-group">
                <label for="librarian_id">ID бібліотекаря:</label>
                <input type="number" id="librarian_id" name="librarian_id" required>
            </div>

            <div class="form-group">
                <label for="reader_id">ID читача:</label>
                <input type="number" id="reader_id" name="reader_id" required>
            </div>

            <div class="form-group">
                <label for="book_code">Код книги:</label>
                <input type="text" id="book_code" name="book_code" required>
            </div>

            <div class="form-group">
                <label for="actual_return">Дата фактичного повернення:</label>
                <input type="date" id="actual_return" name="actual_return" required>
            </div>

            <button type="submit">Додати повернення</button>
        </form>
    </div>

</body>
</html>
