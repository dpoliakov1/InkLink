<?php
require_once '../constants.php';

$con = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME, DB_PORT) or die(mysqli_error($con));

if (!$con) {
    die("Не вдалося підключитися до бази даних: " . mysqli_connect_error());
}

function getSubscriptions() {
    global $con;

    $query = "SELECT sub_id, sub_type, start_date, end_date, status, sub_price FROM subscriptions";
    $result = mysqli_query($con, $query);

    if (!$result) {
        die("Помилка виконання запиту: " . mysqli_error($con));
    }

    $subscriptions = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $subscriptions[] = $row;
    }

    return $subscriptions;
}

function deleteSubscription($sub_id) {
    global $con;

    if (!filter_var($sub_id, FILTER_VALIDATE_INT)) {
        die("Невірний ідентифікатор підписки.");
    }

    $delete_query = "DELETE FROM subscriptions WHERE sub_id = ?";
    $stmt = mysqli_prepare($con, $delete_query);
    mysqli_stmt_bind_param($stmt, "i", $sub_id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: index.php?message=Підписка успішно видалена.");
        exit;
    } else {
        header("Location: index.php?error=Помилка при видаленні підписки.");
        exit;
    }

    mysqli_stmt_close($stmt);
}

if (isset($_GET['delete_sub_id'])) {
    deleteSubscription($_GET['delete_sub_id']);
}

$subscriptions = getSubscriptions();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список підписок</title>
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

        .subscription-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            padding: 20px;
            width: 90%;
            max-width: 1500px;
        }

        .subscription-item {
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .subscription-item:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        .subscription-item h3 {
            font-size: 20px;
            color: #2a2a2a;
            margin-bottom: 10px;
        }

        .subscription-item p {
            color: #555;
            margin: 8px 0;
            font-size: 16px;
        }

        .subscription-item strong {
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

    <h1>Список підписок</h1>

    <?php if (isset($_GET['message'])): ?>
        <p style="color: green;"><?php echo htmlspecialchars($_GET['message']); ?></p>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <p style="color: red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
    <?php endif; ?>

    <?php if ($subscriptions): ?>
        <div class="subscription-list">
            <?php foreach ($subscriptions as $subscription): ?>
                <div class="subscription-item">
                    <h3>Підписка #<?php echo htmlspecialchars($subscription['sub_id']); ?></h3>
                    <p><strong>Тип:</strong> <?php echo htmlspecialchars($subscription['sub_type']); ?></p>
                    <p><strong>Дата початку:</strong> <?php echo htmlspecialchars($subscription['start_date']); ?></p>
                    <p><strong>Дата закінчення:</strong> <?php echo htmlspecialchars($subscription['end_date']); ?></p>
                    <p><strong>Статус:</strong> <?php echo $subscription['status'] ? 'Активна' : 'Неактивна'; ?></p>
                    <p><strong>Ціна:</strong> <?php echo htmlspecialchars($subscription['sub_price']); ?> грн</p>

                    <a href="?delete_sub_id=<?php echo htmlspecialchars($subscription['sub_id']); ?>" class="delete-link" onclick="return confirm('Ви впевнені, що хочете видалити цю підписку?');">Видалити</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Підписок не знайдено.</p>
    <?php endif; ?>

</body>
</html>
