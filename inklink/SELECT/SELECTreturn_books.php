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
        $return_books[] = array_map('htmlspecialchars', $row);
    }

    return $return_books;
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
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
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
    </style>
</head>
<body>

    <h1>Список повернень книг</h1>

    <?php if ($return_books): ?>
        <div class="return-list">
            <?php foreach ($return_books as $return): ?>
                <div class="return-item">
                    <h3>Повернення #<?php echo $return['return_id']; ?></h3>
                    <p><strong>ID бібліотекаря:</strong> <?php echo $return['librarian_id']; ?></p>
                    <p><strong>ID читача:</strong> <?php echo $return['reader_id']; ?></p>
                    <p><strong>Код книги:</strong> <?php echo $return['book_code']; ?></p>
                    <p><strong>Дата фактичного повернення:</strong> <?php echo $return['actual_return']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Повернень книг не знайдено.</p>
    <?php endif; ?>

</body>
</html>
