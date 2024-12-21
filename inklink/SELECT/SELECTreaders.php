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
        $readers[] = array_map('htmlspecialchars', $row);
    }

    return $readers;
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
    </style>
</head>
<body>

    <h1>Читачі бібліотеки</h1>

    <?php if ($readers): ?>
        <div class="readers-list">
            <?php foreach ($readers as $reader): ?>
                <div class="reader-item">
                    <h3>Читач ID: <?php echo $reader['reader_id']; ?></h3>
                    <p><strong>Ім'я та прізвище:</strong> <?php echo $reader['name_reader']; ?></p>
                    <p><strong>Телефон:</strong> <?php echo $reader['phone_num']; ?></p>
                    <p><strong>Дата народження:</strong> <?php echo $reader['birth_date']; ?></p>
                    <p><strong>Підписка ID:</strong> <?php echo $reader['sub_id']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Читачів не знайдено.</p>
    <?php endif; ?>

</body>
</html>
