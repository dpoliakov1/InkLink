<?php
require_once '../constants.php';

$con = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME, DB_PORT) or die(mysqli_error($con));

if (!$con) {
    die("Не вдалося підключитися до бази даних: " . mysqli_connect_error());
}

function getReaders() {
    global $con;

    $query = "SELECT reader_id, name_reader, phone_num, birth_date, sub_id FROM readers";
    $result = mysqli_query($con, $query);

    if (!$result) {
        die("Помилка виконання запиту: " . mysqli_error($con));
    }

    $readers = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $readers[] = $row;
    }

    return $readers;
}

function deleteReader($reader_id) {
    global $con;

    if (!filter_var($reader_id, FILTER_VALIDATE_INT)) {
        die("Невірний ідентифікатор читача.");
    }

    $query = "DELETE FROM readers WHERE reader_id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, 'i', $reader_id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: index.php?message=Читача успішно видалено.");
        exit;
    } else {
        header("Location: index.php?error=Помилка при видаленні читача.");
        exit;
    }

    mysqli_stmt_close($stmt);
}

if (isset($_GET['delete_id'])) {
    deleteReader($_GET['delete_id']);
}

$readers = getReaders();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Читачі бібліотеки</title>
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

        .readers-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 30px;
            padding: 20px;
            width: 90%;
            max-width: 1500px;
        }

        .reader-item {
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .reader-item:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        .reader-item h3 {
            font-size: 20px;
            color: #2a2a2a;
            margin-bottom: 10px;
        }

        .reader-item p {
            color: #555;
            margin: 8px 0;
            font-size: 16px;
        }

        .reader-item strong {
            color: #333;
            font-weight: 600;
        }

        .delete-btn {
            background-color: red;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            text-decoration: none;
            text-align: center;
            display: inline-block;
        }

        .delete-btn:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>

    <h1>Читачі бібліотеки</h1>

    <?php if (isset($_GET['message'])): ?>
        <p style="color: green;"><?php echo htmlspecialchars($_GET['message']); ?></p>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <p style="color: red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
    <?php endif; ?>

    <?php if ($readers): ?>
        <div class="readers-list">
            <?php foreach ($readers as $reader): ?>
                <div class="reader-item">
                    <h3>Читач ID: <?php echo htmlspecialchars($reader['reader_id']); ?></h3>
                    <p><strong>Ім'я та прізвище:</strong> <?php echo htmlspecialchars($reader['name_reader']); ?></p>
                    <p><strong>Телефон:</strong> <?php echo htmlspecialchars($reader['phone_num']); ?></p>
                    <p><strong>Дата народження:</strong> <?php echo htmlspecialchars($reader['birth_date']); ?></p>
                    <p><strong>Підписка ID:</strong> <?php echo htmlspecialchars($reader['sub_id']); ?></p>
                    <a href="?delete_id=<?php echo htmlspecialchars($reader['reader_id']); ?>" class="delete-btn" onclick="return confirm('Ви впевнені, що хочете видалити цього читача?');">Видалити</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Читачів не знайдено.</p>
    <?php endif; ?>

</body>
</html>
