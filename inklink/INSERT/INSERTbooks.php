<?php
require_once '../constants.php';

function connectToDatabase() {
    $con = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    if (!$con) {
        die("Не вдалося підключитися до бази даних: " . mysqli_connect_error());
    }
    return $con;
}

function isBookCodeUnique($con, $book_code) {
    $query = "SELECT COUNT(*) as count FROM books WHERE book_code = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "s", $book_code);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row['count'] == 0;
}

function isSubscriptionIdValid($con, $sub_id) {
    $query = "SELECT COUNT(*) as count FROM subscriptions WHERE sub_id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $sub_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row['count'] > 0;
}

function addBook($con, $book_code, $name, $author, $genre, $limit_age, $availibility, $sub_id) {
    $book_code = filter_var($book_code, FILTER_SANITIZE_STRING);
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $author = filter_var($author, FILTER_SANITIZE_STRING);
    $genre = filter_var($genre, FILTER_SANITIZE_STRING);

    if (!isBookCodeUnique($con, $book_code)) {
        return "Книга з таким кодом вже існує!";
    }

    if (!isSubscriptionIdValid($con, $sub_id)) {
        return "Помилка: Невірний ID підписки!";
    }

    $query = "INSERT INTO books (book_code, name, author, genre, limit_age, availibility, sub_id)
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "ssssiii", $book_code, $name, $author, $genre, $limit_age, $availibility, $sub_id);

    if (mysqli_stmt_execute($stmt)) {
        return "Книга успішно додана!";
    } else {
        return "Помилка при додаванні книги: " . mysqli_error($con);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $con = connectToDatabase();

    $book_code = $_POST['book_code'];
    $name = $_POST['name'];
    $author = $_POST['author'];
    $genre = $_POST['genre'];
    $limit_age = $_POST['limit_age'];
    $availibility = isset($_POST['availibility']) ? 1 : 0;
    $sub_id = $_POST['sub_id'];

    $message = addBook($con, $book_code, $name, $author, $genre, $limit_age, $availibility, $sub_id);
    echo "<script>alert('$message');</script>";

    mysqli_close($con);
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Додати нову книгу</title>
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

        input[type="text"], input[type="number"], select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus, input[type="number"]:focus, select:focus {
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

    <h1>Додати нову книгу</h1>

    <div class="form-container">
        <form method="post" action="">
            <div class="form-group">
                <label for="book_code">Код книги:</label>
                <input type="text" id="book_code" name="book_code" required>
            </div>

            <div class="form-group">
                <label for="name">Назва книги:</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="author">Автор:</label>
                <input type="text" id="author" name="author" required>
            </div>

            <div class="form-group">
                <label for="genre">Жанр:</label>
                <input type="text" id="genre" name="genre" required>
            </div>

            <div class="form-group">
                <label for="limit_age">Вікові обмеження:</label>
                <select id="limit_age" name="limit_age" required>
                    <option value="10">10</option>
                    <option value="12">12</option>
                    <option value="14">14</option>
                    <option value="16">16</option>
                    <option value="18">18</option>
                </select>
            </div>

            <div class="form-group">
                <label for="availibility">Наявність:</label>
                <input type="checkbox" id="availibility" name="availibility" value="1"> В наявності
            </div>

            <div class="form-group">
                <label for="sub_id">ID підписки:</label>
                <input type="number" id="sub_id" name="sub_id" required>
            </div>

            <button type="submit">Додати книгу</button>
        </form>
    </div>

</body>
</html>
