<?php
require_once '../constants.php';

$con = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME, DB_PORT) or die(mysqli_error($con));

if (!$con) {
    die("Не вдалося підключитися до бази даних: " . mysqli_connect_error());
}

function getBooks() {
    global $con;
    $query = "SELECT book_code, name, author, genre, limit_age, availibility, sub_id FROM books";
    return mysqli_query($con, $query);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_code'])) {
    $book_code = $_POST['book_code'];
    $name = $_POST['name'];
    $author = $_POST['author'];
    $genre = $_POST['genre'];
    $limit_age = $_POST['limit_age'];
    $availibility = $_POST['availibility'];
    $sub_id = $_POST['sub_id'];

    $update_query = "UPDATE books SET name = ?, author = ?, genre = ?, limit_age = ?, availibility = ?, sub_id = ? WHERE book_code = ?";
    $stmt = mysqli_prepare($con, $update_query);
    mysqli_stmt_bind_param($stmt, 'sssiiis', $name, $author, $genre, $limit_age, $availibility, $sub_id, $book_code);

    if (mysqli_stmt_execute($stmt)) {
        echo "<p style='color: green;'>Дані книги успішно оновлено!</p>";
    } else {
        echo "<p style='color: red;'>Помилка оновлення даних: " . mysqli_error($con) . "</p>";
    }

    mysqli_stmt_close($stmt);
}

$books_result = getBooks();
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оновлення книги</title>
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

        input[type="text"], input[type="number"], select {
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
    <h1>Оновлення книги</h1>

    <h2>Список книг:</h2>
    <table>
        <tr>
            <th>Код книги</th>
            <th>Назва</th>
            <th>Автор</th>
            <th>Жанр</th>
            <th>Вікові обмеження</th>
            <th>Наявність</th>
            <th>Підписка ID</th>
            <th>Редагувати</th>
        </tr>
        <?php while ($book = mysqli_fetch_assoc($books_result)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($book['book_code']); ?></td>
                <td><?php echo htmlspecialchars($book['name']); ?></td>
                <td><?php echo htmlspecialchars($book['author']); ?></td>
                <td><?php echo htmlspecialchars($book['genre']); ?></td>
                <td><?php echo htmlspecialchars($book['limit_age']); ?></td>
                <td><?php echo $book['availibility'] == 1 ? 'В наявності' : 'Немає в наявності'; ?></td>
                <td><?php echo htmlspecialchars($book['sub_id']); ?></td>
                <td><a href="UPDATEbooks.php?book_code=<?php echo urlencode($book['book_code']); ?>">Редагувати</a></td>
            </tr>
        <?php } ?>
    </table>

    <?php
    if (isset($_GET['book_code'])) {
        $book_code = $_GET['book_code'];
        $stmt = mysqli_prepare($con, "SELECT * FROM books WHERE book_code = ?");
        mysqli_stmt_bind_param($stmt, 's', $book_code);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $book = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
    ?>

    <h2>Редагувати книгу: <?php echo htmlspecialchars($book['name']); ?></h2>
    <form method="POST">
        <input type="hidden" name="book_code" value="<?php echo htmlspecialchars($book['book_code']); ?>">

        <label for="name">Назва:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($book['name']); ?>" required>

        <label for="author">Автор:</label>
        <input type="text" name="author" value="<?php echo htmlspecialchars($book['author']); ?>" required>

        <label for="genre">Жанр:</label>
        <input type="text" name="genre" value="<?php echo htmlspecialchars($book['genre']); ?>" required>

        <label for="limit_age">Вікові обмеження:</label>
        <input type="number" name="limit_age" value="<?php echo htmlspecialchars($book['limit_age']); ?>" required>

        <label for="availibility">Наявність:</label>
        <select name="availibility">
            <option value="1" <?php if ($book['availibility'] == 1) echo 'selected'; ?>>В наявності</option>
            <option value="0" <?php if ($book['availibility'] == 0) echo 'selected'; ?>>Немає в наявності</option>
        </select>

        <label for="sub_id">Підписка ID:</label>
        <input type="number" name="sub_id" value="<?php echo htmlspecialchars($book['sub_id']); ?>" required>

        <button type="submit">Зберегти зміни</button>
    </form>

    <?php } ?>

</body>
</html>
