<?php
require_once '../constants.php';

$con = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME, DB_PORT) or die(mysqli_error($con));

if (!$con) {
    die("Не вдалося підключитися до бази даних: " . mysqli_connect_error());
}

function getReturnBooks() {
    global $con;

    $query = "SELECT return_id, librarian_id, reader_id, book_code, actual_return FROM return_books";
    $result = mysqli_query($con, $query);

    if (!$result) {
        die("Помилка виконання запиту: " . mysqli_error($con));
    }

    $return_books = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $return_books[] = $row;
    }

    return $return_books;
}

function deleteReturnRecord($return_id) {
    global $con;

    if (!filter_var($return_id, FILTER_VALIDATE_INT)) {
        die("Невірний ідентифікатор повернення.");
    }

    $delete_query = "DELETE FROM return_books WHERE return_id = ?";
    $stmt = mysqli_prepare($con, $delete_query);
    mysqli_stmt_bind_param($stmt, "i", $return_id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: index.php?message=Запис успішно видалено.");
        exit;
    } else {
        header("Location: index.php?error=Помилка при видаленні запису.");
        exit;
    }

    mysqli_stmt_close($stmt);
}

if (isset($_GET['delete_return_id'])) {
    deleteReturnRecord($_GET['delete_return_id']);
}

$return_books = getReturnBooks();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список повернень книг</title>
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

        .return-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 30px;
            padding: 20px;
            width: 90%;
            max-width: 1500px;
        }

        .return-item {
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .return-item:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        .return-item h3 {
            font-size: 20px;
            color: #2a2a2a;
            margin-bottom: 10px;
        }

        .return-item p {
            color: #555;
            margin: 8px 0;
            font-size: 16px;
        }

        .return-item strong {
            color: #333;
            font-weight: 600;
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

    <h1>Список повернень книг</h1>

    <?php if (isset($_GET['message'])): ?>
        <p style="color: green;"><?php echo htmlspecialchars($_GET['message']); ?></p>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <p style="color: red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
    <?php endif; ?>

    <?php if ($return_books): ?>
        <div class="return-list">
            <?php foreach ($return_books as $return): ?>
                <div class="return-item">
                    <h3>Повернення #<?php echo htmlspecialchars($return['return_id']); ?></h3>
                    <p><strong>ID бібліотекаря:</strong> <?php echo htmlspecialchars($return['librarian_id']); ?></p>
                    <p><strong>ID читача:</strong> <?php echo htmlspecialchars($return['reader_id']); ?></p>
                    <p><strong>Код книги:</strong> <?php echo htmlspecialchars($return['book_code']); ?></p>
                    <p><strong>Дата повернення:</strong> <?php echo htmlspecialchars($return['actual_return']); ?></p>

                    <a href="?delete_return_id=<?php echo htmlspecialchars($return['return_id']); ?>" class="delete-link" onclick="return confirm('Ви впевнені, що хочете видалити цей запис?');">Видалити</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Повернення книг не знайдено.</p>
    <?php endif; ?>

</body>
</html>
