<?php
require_once '../constants.php';

$con = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME, DB_PORT) or die(mysqli_error($con));

if (!$con) {
    die("Не вдалося підключитися до бази даних: " . mysqli_connect_error());
}

function getIssuedBooks($con) {
    $query = "SELECT issue_id, librarian_id, reader_id, book_code, date_issue, date_return, status FROM issued_books";
    $stmt = $con->prepare($query);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

if (isset($_GET['issue_id'])) {
    $issue_id = $_GET['issue_id'];
    $query = "SELECT * FROM issued_books WHERE issue_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $issue_id);
    $stmt->execute();
    $issued_book = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $issue_id = $_POST['issue_id'];
    $librarian_id = $_POST['librarian_id'];
    $reader_id = $_POST['reader_id'];
    $book_code = $_POST['book_code'];
    $date_issue = $_POST['date_issue'];
    $date_return = $_POST['date_return'];
    $status = $_POST['status'];

    $update_query = "UPDATE issued_books SET librarian_id = ?, reader_id = ?, book_code = ?, date_issue = ?, date_return = ?, status = ? WHERE issue_id = ?";
    $stmt = $con->prepare($update_query);
    $stmt->bind_param("iissssi", $librarian_id, $reader_id, $book_code, $date_issue, $date_return, $status, $issue_id);

    if ($stmt->execute()) {
        header("Location: ?");
        exit();
    }
    $stmt->close();
}

$issued_books = getIssuedBooks($con);
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
        .edit-button {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 15px;
            color: #fff;
            background-color: #007bff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .edit-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <?php if (isset($issue_id) && isset($issued_book)): ?>
        <h1>Редагування видачі книги</h1>
        <form method="post" action="">
            <input type="hidden" name="issue_id" value="<?php echo htmlspecialchars($issued_book['issue_id']); ?>">

            <label>Бібліотекар ID: <input type="number" name="librarian_id" value="<?php echo htmlspecialchars($issued_book['librarian_id']); ?>" required></label><br>
            <label>Читач ID: <input type="number" name="reader_id" value="<?php echo htmlspecialchars($issued_book['reader_id']); ?>" required></label><br>
            <label>Код книги: <input type="text" name="book_code" value="<?php echo htmlspecialchars($issued_book['book_code']); ?>" required></label><br>
            <label>Дата видачі: <input type="date" name="date_issue" value="<?php echo htmlspecialchars($issued_book['date_issue']); ?>" required></label><br>
            <label>Дата повернення: <input type="date" name="date_return" value="<?php echo htmlspecialchars($issued_book['date_return']); ?>" required></label><br>
            <label>Статус:
                <select name="status">
                    <option value="returned" <?php if ($issued_book['status'] == 'returned') echo 'selected'; ?>>Повернуто</option>
                    <option value="not-returned" <?php if ($issued_book['status'] == 'not-returned') echo 'selected'; ?>>Не повернуто</option>
                </select>
            </label><br><br>

            <button type="submit">Оновити</button>
        </form>
    <?php else: ?>
        <h1>Видані книги</h1>

        <?php if ($issued_books): ?>
            <div class="issued-books-list">
                <?php foreach ($issued_books as $issued_book): ?>
                    <div class="issued-book-item">
                        <h3>Issue ID: <?php echo htmlspecialchars($issued_book['issue_id']); ?></h3>
                        <p><strong>Бібліотекар:</strong> <?php echo htmlspecialchars($issued_book['librarian_id']); ?></p>
                        <p><strong>Читач:</strong> <?php echo htmlspecialchars($issued_book['reader_id']); ?></p>
                        <p><strong>Код книги:</strong> <?php echo htmlspecialchars($issued_book['book_code']); ?></p>
                        <p><strong>Дата видачі:</strong> <?php echo htmlspecialchars($issued_book['date_issue']); ?></p>
                        <p><strong>Дата повернення:</strong> <?php echo htmlspecialchars($issued_book['date_return']); ?></p>
                        <p><strong>Статус:</strong>
                            <?php
                                if ($issued_book['status'] == 'returned') {
                                    echo '<span class="status returned">Повернуто</span>';
                                } else {
                                    echo '<span class="status not-returned">Не повернуто</span>';
                                }
                            ?>
                        </p>
                        <p>
                            <a href="?issue_id=<?php echo urlencode($issued_book['issue_id']); ?>" class="edit-button">Редагувати</a>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Виданих книг не знайдено.</p>
        <?php endif; ?>
    <?php endif; ?>

</body>
</html>
