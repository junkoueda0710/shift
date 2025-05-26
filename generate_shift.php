<?php 

 

session_start(); 

 

// 今月・来月の年月（フォーマット：YYYY-MM） 

$now = new DateTime('first day of this month'); 

$next = new DateTime('first day of next month'); 

 

$year_months = [ 

    $now->format('Y-m') => '今月（' . $now->format('Y年n月') . '）', 

    $next->format('Y-m') => '来月（' . $next->format('Y年n月') . '）' 

]; 

?> 

 

<!DOCTYPE html> 

<html> 

<head> 

    <meta charset="UTF-8"> 

    <title>シフト作成</title> 

</head> 

<body> 

    <h1>自動シフト作成</h1> 

    <form action="generate_result.php" method="GET"> 

        <p>対象月を選んでください：</p> 

        <?php foreach ($year_months as $ym => $label): ?> 

            <label> 

                <input type="radio" name="month" value="<?= $ym ?>" required> 

                <?= $label ?> 

            </label><br> 

        <?php endforeach; ?> 

        <br> 

        <button type="submit">この月でシフトを作成する</button> 

    </form> 

</body> 

</html> 