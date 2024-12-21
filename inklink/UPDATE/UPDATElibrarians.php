<?php
require_once '../constants.php';

$con = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME, DB_PORT) or die(mysqli_error($con));

if (!$con) {
    die("Не вдалося підключитися до бази даних: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
    $librarian_id = $_GET['id'];
    $email = $_POST['email'];
    $libr_name = $_POST['libr_name'];
    $password = $_POST['password'];

    $update_query = "UPDATE librarians SET email = ?, libr_name = ?, password = ? WHERE librarian_id = ?";
    $stmt = mysqli_prepare($con, $update_query);
    mysqli_stmt_bind_param($stmt, "sssi", $email, $libr_name, $password, $librarian_id);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        echo "Дані бібліотекаря успішно оновлено!";
    } else {
        echo "Помилка при оновленні даних.";
    }
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

$librarian = null;
if (isset($_GET['id'])) {
    $librarian_id = $_GET['id'];
    $query = "SELECT * FROM librarians WHERE librarian_id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $librarian_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $librarian = mysqli_fetch_assoc($result);

    if (!$librarian) {
        die("Бібліотекаря не знайдено.");
    }
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
        .edit-link {
            display: inline-block;
            margin-top: 10px;
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        .edit-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <h1>Бібліотекарі</h1>

    <?php if (isset($_GET['id']) && $librarian): ?>
        <h2>Редагування бібліотекаря ID: <?php echo htmlspecialchars($librarian['librarian_id']); ?></h2>
        <form method="post">
            <label>Email:
                <input type="email" name="email" value="<?php echo htmlspecialchars($librarian['email']); ?>" required>
            </label>
            <br>
            <label>Ім'я та прізвище:
                <input type="text" name="libr_name" value="<?php echo htmlspecialchars($librarian['libr_name']); ?>" required>
            </label>
            <br>
            <label>Пароль:
                <input type="password" name="password" value="<?php echo htmlspecialchars($librarian['password']); ?>" required>
            </label>
            <br>
            <button type="submit">Зберегти зміни</button>
        </form>
        <p><a href="?">Повернутися до списку бібліотекарів</a></p>

    <?php else: ?>
        <?php if ($librarians): ?>
            <div class="librarians-list">
                <?php foreach ($librarians as $librarian): ?>
                    <div class="librarian-item">
                        <h3>Бібліотекар ID: <?php echo htmlspecialchars($librarian['librarian_id']); ?></h3>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($librarian['email']); ?></p>
                        <p><strong>Ім'я та прізвище:</strong> <?php echo htmlspecialchars($librarian['libr_name']); ?></p>
                        <p><strong>Пароль:</strong> <?php echo htmlspecialchars($librarian['password']); ?></p>
                        <p><a href="?id=<?php echo urlencode($librarian['librarian_id']); ?>" class="edit-link">Редагувати</a></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Бібліотекарів не знайдено.</p>
        <?php endif; ?>
    <?php endif; ?>

</body>
</html>
