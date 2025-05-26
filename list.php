<?php 

require 'db.php'; 

 

// スタッフごとの希望休も取得 

$sql = " 

    SELECT s.id, s.name, s.type, GROUP_CONCAT(h.holiday_date ORDER BY h.holiday_date SEPARATOR ', ') AS holidays 

    FROM staff s 

    LEFT JOIN holidays h ON s.id = h.staff_id 

    GROUP BY s.id 

    ORDER BY s.id DESC 

"; 

$stmt = $pdo->query($sql); 

$staffs = $stmt->fetchAll(PDO::FETCH_ASSOC); 

?> 

 

<!DOCTYPE html> 

<html> 

<head> 

    <meta charset="UTF-8"> 

    <title>スタッフ一覧</title> 

</head> 

<body> 

    <h1>スタッフ一覧</h1> 

    <table border="1" cellpadding="8"> 

        <tr> 

            <th>名前</th> 

            <th>勤務形態</th> 

            <th>希望休</th> 

        </tr> 

        <?php foreach ($staffs as $staff): ?> 

            <tr> 

                <td><?= htmlspecialchars($staff['name']) ?></td> 

                <td><?= htmlspecialchars($staff['type']) ?></td> 

                <td><?= htmlspecialchars($staff['holidays']) ?: '-' ?></td> 

            </tr> 

        <?php endforeach; ?> 

    </table> 

 

    <br> 

    <a href="index.php">← 登録画面へ戻る</a> 

</body> 

</html> 