<?php 

require 'db.php'; 

 

// スタッフ登録処理 

if ($_SERVER['REQUEST_METHOD'] === 'POST') { 

    $name = $_POST['name']; 

    $type = $_POST['type']; 

    $days = $_POST['days'] ?? []; 

 

    // 空でない日付のみカウントして、最大4日チェック 

    $valid_days = array_filter($days); // 空じゃない日付だけ残す 

 

    if (count($valid_days) > 4) { 

        echo "希望休は最大4日までです。"; 

    } else { 

        // スタッフ情報を保存 

        $stmt = $pdo->prepare("INSERT INTO staff (name, type) VALUES (?, ?)"); 

        $stmt->execute([$name, $type]); 

        $staff_id = $pdo->lastInsertId(); 

 

        // 希望休を保存（空のやつは除外） 

        $stmt = $pdo->prepare("INSERT INTO holidays (staff_id, holiday_date) VALUES (?, ?)"); 

        foreach ($valid_days as $day) { 

            $stmt->execute([$staff_id, $day]); 

        } 

 

        echo "登録完了！"; 

    } 

} 

?> 

 

<!DOCTYPE html> 

<html> 

<head> 

    <meta charset="UTF-8"> 

    <title>スタッフ登録</title> 

</head> 

<body> 

    <h1>スタッフ登録フォーム</h1> 

    <form method="POST"> 

        名前：<input type="text" name="name" required><br><br> 

 

        勤務形態：<br> 

        <label><input type="radio" name="type" value="出勤" required> 出勤</label><br> 

        <label><input type="radio" name="type" value="在宅" required> 在宅</label><br><br> 

 

        希望休（最大4日まで）：<br> 

        <input type="date" name="days[]"><br> 

        <input type="date" name="days[]"><br> 

        <input type="date" name="days[]"><br> 

        <input type="date" name="days[]"><br><br> 

 

        <button type="submit">登録</button> 

    </form> 

</body> 

</html> 