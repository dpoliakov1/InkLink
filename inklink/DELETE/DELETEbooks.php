<?php
require_once '../constants.php';

$con = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME, DB_PORT) or die(mysqli_error($con));

if (!$con) {
    die("Не вдалося підключитися до бази даних: " . mysqli_connect_error());
}

function getBooks() {
    global $con;

    $query = "SELECT name, author, genre, book_code, limit_age, availibility, sub_id FROM books";
    $result = mysqli_query($con, $query);

    if (!$result) {
        die("Помилка виконання запиту: " . mysqli_error($con));
    }

    $books = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $books[] = $row;
    }

    return $books;
}

if (isset($_GET['delete_book_code'])) {
    $book_code_to_delete = filter_var($_GET['delete_book_code'], FILTER_SANITIZE_STRING);

    if (!preg_match('/^[A-Za-z0-9_-]+$/', $book_code_to_delete)) {
        echo "<script>alert('Неправильний код книги!'); window.location.href = 'index.php';</script>";
        exit;
    }

    $delete_query = "DELETE FROM books WHERE book_code = ?";
    $stmt = mysqli_prepare($con, $delete_query);
    mysqli_stmt_bind_param($stmt, "s", $book_code_to_delete);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Книга успішно видалена!'); window.location.href = 'index.php';</script>";
    } else {
        echo "<script>alert('Помилка при видаленні книги.'); window.location.href = 'index.php';</script>";
    }

    mysqli_stmt_close($stmt);
}

$books = getBooks();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список книг бібліотеки</title>
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

        .book-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 30px;
            padding: 20px;
            width: 90%;
            max-width: 1500px;
        }

        .book-item {
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .book-item:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        .book-item h3 {
            font-size: 20px;
            color: #2a2a2a;
            margin-bottom: 10px;
        }

        .book-item p {
            color: #555;
            margin: 8px 0;
            font-size: 16px;
        }

        .book-item strong {
            color: #333;
            font-weight: 600;
        }

        .availability {
            font-weight: bold;
            padding: 5px;
            border-radius: 5px;
        }

        .availability.in-stock {
            background-color: #4CAF50;
            color: white;
        }

        .availability.out-of-stock {
            background-color: #f44336;
            color: white;
        }

        .delete-link {
            display: inline-block;
            margin-top: 10px;
            padding: 5px 10px;
            background-color: #f44336;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .delete-link:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>

    <h1>Список книг бібліотеки</h1>

    <?php if ($books): ?>
        <div class="book-list">
            <?php foreach ($books as $book): ?>
                <div class="book-item">
                    <h3><?php echo htmlspecialchars($book['name']); ?></h3>
                    <p><strong>Автор:</strong> <?php echo htmlspecialchars($book['author']); ?></p>
                    <p><strong>Жанр:</strong> <?php echo htmlspecialchars($book['genre']); ?></p>
                    <p><strong>Код книги:</strong> <?php echo htmlspecialchars($book['book_code']); ?></p>
                    <p><strong>Вікові обмеження:</strong> <?php echo htmlspecialchars($book['limit_age']); ?></p>

                    <p><strong>Наявність:</strong>
                        <?php
                            if ($book['availibility'] == 1) {
                                echo '<span class="availability in-stock">В наявності</span>';
                            } else {
                                echo '<span class="availability out-of-stock">Немає в наявності</span>';
                            }
                        ?>
                    </p>

                    <p><strong>Підписка ID:</strong> <?php echo htmlspecialchars($book['sub_id']); ?></p>

                    <a href="?delete_book_code=<?php echo urlencode($book['book_code']); ?>" class="delete-link" onclick="return confirm('Ви впевнені, що хочете видалити цю книгу?');">Видалити</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Книги не знайдено.</p>
    <?php endif; ?>

</body>
</html>
