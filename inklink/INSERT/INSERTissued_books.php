<?php
require_once '../constants.php';

function connectToDatabase() {
    $con = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    if (!$con) {
        die("Не вдалося підключитися до бази даних: " . mysqli_connect_error());
    }
    return $con;
}

function isLibrarianValid($con, $librarian_id) {
    $query = "SELECT COUNT(*) as count FROM librarians WHERE librarian_id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $librarian_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row['count'] > 0;
}

function isReaderValid($con, $reader_id) {
    $query = "SELECT COUNT(*) as count FROM readers WHERE reader_id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $reader_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row['count'] > 0;
}

function doesBookExist($con, $book_code) {
    $query = "SELECT COUNT(*) as count FROM books WHERE book_code = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "s", $book_code);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row['count'] > 0;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['librarian_id'], $_POST['reader_id'], $_POST['book_code'], $_POST['date_issue'], $_POST['date_return'])) {
        $con = connectToDatabase();

        $librarian_id = filter_var($_POST['librarian_id'], FILTER_VALIDATE_INT);
        $reader_id = filter_var($_POST['reader_id'], FILTER_VALIDATE_INT);
        $book_code = filter_var($_POST['book_code'], FILTER_SANITIZE_STRING);
        $date_issue = filter_var($_POST['date_issue'], FILTER_SANITIZE_STRING);
        $date_return = filter_var($_POST['date_return'], FILTER_SANITIZE_STRING);

        if (!$librarian_id || !isLibrarianValid($con, $librarian_id)) {
            echo "<script>alert('Бібліотекаря з таким ID не існує!');</script>";
            exit;
        }

        if (!$reader_id || !isReaderValid($con, $reader_id)) {
            echo "<script>alert('Читача з таким ID не існує!');</script>";
            exit;
        }

        if (!doesBookExist($con, $book_code)) {
            echo "<script>alert('Книги з таким кодом не існує!');</script>";
            exit;
        }

        if (strtotime($date_issue) > strtotime($date_return)) {
            echo "<script>alert('Дата видачі не може бути пізніше дати повернення!');</script>";
            exit;
        }

        $status = 1;
        $stmt = mysqli_prepare($con, "INSERT INTO issued_books (librarian_id, reader_id, book_code, date_issue, date_return, status) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iisssi", $librarian_id, $reader_id, $book_code, $date_issue, $date_return, $status);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Книга успішно видана!');</script>";
        } else {
            echo "<script>alert('Помилка при видачі книги.');</script>";
        }

        mysqli_stmt_close($stmt);
        mysqli_close($con);
    } else {
        echo "<script>alert('Заповніть усі обов'язкові поля!');</script>";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Видати книгу</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        h2 {
            text-align: center;
            margin-top: 0;
            margin-bottom: 30px;
            color: #333;
            font-size: 1.8em;
        }

        .form-container {
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
        }

        input[type="number"], input[type="text"], input[type="date"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        input[type="number"]:focus, input[type="text"]:focus, input[type="date"]:focus {
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

    <div class="form-container">
        <h2>Видати книгу</h2>
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
                <label for="date_issue">Дата видачі:</label>
                <input type="date" id="date_issue" name="date_issue" required>
            </div>

            <div class="form-group">
                <label for="date_return">Дата повернення:</label>
                <input type="date" id="date_return" name="date_return" required>
            </div>

            <button type="submit">Видати книгу</button>
        </form>
    </div>

</body>
</html>
