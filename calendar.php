<?php 

require 'functions.php'; 

require 'db.php'; // ←DB接続ファイルを使ってる場合 

 

// スタッフ一覧取得 

$staffs = getStaffList($pdo); 

 

// 選択されたスタッフID（なければ最初のスタッフ） 

$staff_id = isset($_GET['staff_id']) ? (int)$_GET['staff_id'] : $staffs[0]['id']; 

 

// 月・年の取得（なければ今月） 

$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y'); 

$month = isset($_GET['month']) ? (int)$_GET['month'] : date('n'); 

 

// シフト取得 

$shifts = getShiftsForMonth($pdo, $staff_id, $year, $month); 

?> 

<!DOCTYPE html> 

<html> 

<head> 

    <meta charset="UTF-8"> 

    <title>シフトカレンダー</title> 

    <style> 

        table { border-collapse: collapse; width: 100%; } 

        th, td { border: 1px solid #ccc; text-align: center; padding: 5px; } 

        .holiday { background-color: #f88; }   /* 休み＝赤 */ 

        .work { background-color: #88f; color: white; }    /* 出勤＝青 */ 

        .remote { background-color: #ff8; }   /* 在宅＝黄色 */ 

    </style> 

</head> 

<body> 

<h2>担当者別シフトカレンダー（<?= $year ?>年<?= $month ?>月）</h2> 

 

<form method="get"> 

    担当者: 

    <select name="staff_id" onchange="this.form.submit()"> 

        <?php foreach ($staffs as $staff): ?> 

            <option value="<?= $staff['id'] ?>" <?= $staff['id'] == $staff_id ? 'selected' : '' ?>> 

                <?= htmlspecialchars($staff['name']) ?> 

            </option> 

        <?php endforeach; ?> 

    </select> 

    <input type="hidden" name="year" value="<?= $year ?>"> 

    <input type="hidden" name="month" value="<?= $month ?>"> 

</form> 

 

<!-- 月移動ナビ --> 

<p> 

    <a href="?staff_id=<?= $staff_id ?>&year=<?= $year ?>&month=<?= $month - 1 ?>">前の月</a> | 

    <a href="?staff_id=<?= $staff_id ?>&year=<?= $year ?>&month=<?= $month + 1 ?>">次の月</a> 

</p> 

 

<?= renderShiftCalendar($year, $month, $shifts) ?> 

 

</body> 

</html> 