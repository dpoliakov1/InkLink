<?php
require_once '../constants.php';

$con = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME, DB_PORT) or die(mysqli_error($con));

if (!$con) {
    die("Не вдалося підключитися до бази даних: " . mysqli_connect_error());
}

if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    if (!filter_var($delete_id, FILTER_VALIDATE_INT)) {
        die("Невірний ID.");
    }
    $delete_query = "DELETE FROM librarians WHERE librarian_id = ?";
    $stmt = mysqli_prepare($con, $delete_query);
    mysqli_stmt_bind_param($stmt, 'i', $delete_id);
    if (mysqli_stmt_execute($stmt)) {
        header("Location: index.php?message=Бібліотекаря видалено успішно!");
        exit;
    } else {
        header("Location: index.php?error=Помилка при видаленні.");
        exit;
    }
    mysqli_stmt_close($stmt);
}

function getLibrarians() {
    global $con;
    $query = "SELECT librarian_id, email, libr_name, password FROM librarians";
    $result = mysqli_query($con, $query);
    if (!$result) {
        die("Помилка виконання запиту: " . mysqli_error($con));
    }
    $librarians = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $librarians[] = $row;
    }
    return $librarians;
}

$librarians = getLibrarians();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Бібліотекарі</title>
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

        .librarians-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 30px;
            padding: 20px;
            width: 90%;
            max-width: 1500px;
        }

        .librarian-item {
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .librarian-item:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        .librarian-item h3 {
            font-size: 20px;
            color: #2a2a2a;
            margin-bottom: 10px;
        }

        .librarian-item p {
            color: #555;
            margin: 8px 0;
            font-size: 16px;
        }

        .librarian-item strong {
            color: #333;
            font-weight: 600;
        }

        .delete-btn {
            background-color: #f44336;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .delete-btn:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>

    <h1>Бібліотекарі</h1>

    <?php if (isset($_GET['message'])): ?>
        <p style="color: green;"><?php echo htmlspecialchars($_GET['message']); ?></p>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <p style="color: red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
    <?php endif; ?>

    <?php if ($librarians): ?>
        <div class="librarians-list">
            <?php foreach ($librarians as $librarian): ?>
                <div class="librarian-item">
                    <h3>Бібліотекар ID: <?php echo htmlspecialchars($librarian['librarian_id']); ?></h3>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($librarian['email']); ?></p>
                    <p><strong>Ім'я та прізвище:</strong> <?php echo htmlspecialchars($librarian['libr_name']); ?></p>
                    <p><strong>Пароль:</strong> <?php echo htmlspecialchars($librarian['password']); ?></p>
                    <a href="?delete_id=<?php echo htmlspecialchars($librarian['librarian_id']); ?>" onclick="return confirm('Ви впевнені, що хочете видалити цього бібліотекаря?');">
                        <button class="delete-btn">Видалити</button>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Бібліотекарів не знайдено.</p>
    <?php endif; ?>

</body>
</html>
