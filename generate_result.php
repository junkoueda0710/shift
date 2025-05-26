<?php 

 

require 'db.php'; 

 

if (!isset($_GET['month'])) { 

    echo "月が選ばれていません。"; 

    exit; 

} 

 

$target_month = $_GET['month']; // 形式: YYYY-MM 

$start_date = $target_month . '-01'; 

$end_date = date('Y-m-t', strtotime($start_date)); 

 

// スタッフと希望休のデータ取得 

$stmt = $pdo->query("SELECT * FROM staff"); 

$staffs = $stmt->fetchAll(PDO::FETCH_ASSOC); 

 

$staff_holidays = []; 

foreach ($staffs as $staff) { 

    $stmt = $pdo->prepare("SELECT holiday_date FROM holidays WHERE staff_id = ? AND holiday_date BETWEEN ? AND ?"); 

    $stmt->execute([$staff['id'], $start_date, $end_date]); 

    $holidays = $stmt->fetchAll(PDO::FETCH_COLUMN); 

    $staff_holidays[$staff['id']] = $holidays; 

} 

 

// 対象月の全日リスト 

$days = []; 

$dt = new DateTime($start_date); 

while ($dt->format('Y-m') === $target_month) { 

    $days[] = $dt->format('Y-m-d'); 

    $dt->modify('+1 day'); 

} 

 

// 各スタッフのシフトを週単位で作成 

$staff_shifts = []; 

foreach ($staffs as $staff) { 

    $weekly_shifts = []; 

    $week = []; 

    foreach ($days as $date) { 

        $week[] = $date; 

        if (count($week) == 7 || $date === end($days)) { 

            // 希望休を除いた日を候補にする 

            $available_days = array_diff($week, $staff_holidays[$staff['id']] ?? []); 

            shuffle($available_days); // ランダムに並べ替え 

 

            $shift = []; 

            for ($i = 0; $i < 5 && $i < count($available_days); $i++) { 

                $shift_date = $available_days[$i]; 

                $shift[$shift_date] = $i < 2 ? '出勤' : '在宅'; // 先に2日「出勤」、残り「在宅」 

            } 

 

            $weekly_shifts = array_merge($weekly_shifts, $shift); 

            $week = []; // 次の週へ 

        } 

    } 

    $staff_shifts[$staff['name']] = $weekly_shifts; 

} 

?> 

 

<!DOCTYPE html> 

<html> 

<head> 

    <meta charset="UTF-8"> 

    <title>シフト結果</title> 

</head> 

<body> 

    <h1><?= htmlspecialchars($target_month) ?>のシフト結果</h1> 

    <table border="1" cellpadding="5"> 

        <tr> 

            <th>スタッフ名</th> 

            <?php foreach ($days as $d): ?> 

                <th><?= date('j', strtotime($d)) ?></th> 

            <?php endforeach; ?> 

        </tr> 

        <?php foreach ($staff_shifts as $name => $shifts): ?> 

            <tr> 

                <td><?= htmlspecialchars($name) ?></td> 

                <?php foreach ($days as $d): ?> 

                    <td> 

                        <?= $shifts[$d] ?? '' ?> 

                    </td> 

                <?php endforeach; ?> 

            </tr> 

        <?php endforeach; ?> 

    </table> 

 

    <br> 

    <a href="generate_shift.php">← 月を選びなおす</a> 

</body> 

</html> 