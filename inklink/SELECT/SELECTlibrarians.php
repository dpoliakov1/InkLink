<?php
require_once '../constants.php';

$con = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME, DB_PORT) or die(mysqli_error($con));

if (!$con) {
    die("Не вдалося підключитися до бази даних: " . mysqli_connect_error());
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
        $librarians[] = array_map('htmlspecialchars', $row);
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
    </style>
</head>
<body>

    <h1>Бібліотекарі</h1>

    <?php if ($librarians): ?>
        <div class="librarians-list">
            <?php foreach ($librarians as $librarian): ?>
                <div class="librarian-item">
                    <h3>Бібліотекар ID: <?php echo $librarian['librarian_id']; ?></h3>
                    <p><strong>Email:</strong> <?php echo $librarian['email']; ?></p>
                    <p><strong>Ім'я та прізвище:</strong> <?php echo $librarian['libr_name']; ?></p>
                    <p><strong>Пароль:</strong> <?php echo $librarian['password']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Бібліотекарів не знайдено.</p>
    <?php endif; ?>

</body>
</html>
