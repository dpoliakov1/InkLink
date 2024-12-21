<?php
require_once '../constants.php';

$con = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME, DB_PORT) or die(mysqli_error($con));

if (!$con) {
    die("Не вдалося підключитися до бази даних: " . mysqli_connect_error());
}

function getIssuedBooks() {
    global $con;

    $query = "SELECT issue_id, librarian_id, reader_id, book_code, date_issue, date_return, status FROM issued_books";
    $result = mysqli_query($con, $query);

    if (!$result) {
        die("Помилка виконання запиту: " . mysqli_error($con));
    }

    $issued_books = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $issued_books[] = array_map('htmlspecialchars', $row);
    }

    return $issued_books;
}

$issued_books = getIssuedBooks();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Видані книги</title>
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

        .issued-books-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 30px;
            padding: 20px;
            width: 90%;
            max-width: 1500px;
        }

        .issued-book-item {
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .issued-book-item:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        .issued-book-item h3 {
            font-size: 20px;
            color: #2a2a2a;
            margin-bottom: 10px;
        }

        .issued-book-item p {
            color: #555;
            margin: 8px 0;
            font-size: 16px;
        }

        .issued-book-item strong {
            color: #333;
            font-weight: 600;
        }

        .status {
            font-weight: bold;
            padding: 5px;
            border-radius: 5px;
        }

        .status.returned {
            background-color: #4CAF50;
            color: white;
        }

        .status.not-returned {
            background-color: #f44336;
            color: white;
        }
    </style>
</head>
<body>

    <h1>Видані книги</h1>

    <?php if ($issued_books): ?>
        <div class="issued-books-list">
            <?php foreach ($issued_books as $issued_book): ?>
                <div class="issued-book-item">
                    <h3>Issue ID: <?php echo $issued_book['issue_id']; ?></h3>
                    <p><strong>Бібліотекар:</strong> <?php echo $issued_book['librarian_id']; ?></p>
                    <p><strong>Читач:</strong> <?php echo $issued_book['reader_id']; ?></p>
                    <p><strong>Код книги:</strong> <?php echo $issued_book['book_code']; ?></p>
                    <p><strong>Дата видачі:</strong> <?php echo $issued_book['date_issue']; ?></p>
                    <p><strong>Дата повернення:</strong> <?php echo $issued_book['date_return']; ?></p>

                    <p><strong>Статус:</strong>
                        <?php
                            if ($issued_book['status'] === 'returned') {
                                echo '<span class="status returned">Повернуто</span>';
                            } else {
                                echo '<span class="status not-returned">Не повернуто</span>';
                            }
                        ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Виданих книг не знайдено.</p>
    <?php endif; ?>

</body>
</html>
