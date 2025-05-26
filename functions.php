<?php 

 

function getStaffList($pdo) { 

    $stmt = $pdo->query("SELECT id, name FROM staff"); 

    return $stmt->fetchAll(PDO::FETCH_ASSOC); 

} 

 

function getShiftsForMonth($pdo, $staff_id, $year, $month) { 

    $start = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-01"; 

    $end = date("Y-m-t", strtotime($start)); 

    $stmt = $pdo->prepare("SELECT date, type FROM shifts WHERE staff_id = ? AND date BETWEEN ? AND ?"); 

    $stmt->execute([$staff_id, $start, $end]); 

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC); 

 

    $shifts = []; 

    foreach ($results as $row) { 

        $shifts[$row['date']] = $row['type']; 

    } 

    return $shifts; 

} 

 

function renderShiftCalendar($year, $month, $shifts) { 

    $html = '<table>'; 

    $html .= '<tr>'; 

    $weekdays = ['日','月','火','水','木','金','土']; 

    foreach ($weekdays as $day) { 

        $html .= '<th>' . $day . '</th>'; 

    } 

    $html .= '</tr><tr>'; 

 

    $first_day = date("w", strtotime("$year-$month-01")); 

    $days_in_month = date("t", strtotime("$year-$month-01")); 

    $day_count = 0; 

 

    for ($i = 0; $i < $first_day; $i++) { 

        $html .= '<td></td>'; 

        $day_count++; 

    } 

 

    for ($day = 1; $day <= $days_in_month; $day++) { 

        $date_str = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-" . str_pad($day, 2, '0', STR_PAD_LEFT); 

        $shift = isset($shifts[$date_str]) ? $shifts[$date_str] : ''; 

 

        $class = ''; 

        if ($shift === '休み') $class = 'holiday'; 

        elseif ($shift === '出勤') $class = 'work'; 

        elseif ($shift === '在宅') $class = 'remote'; 

 

        $html .= "<td class='$class'>{$day}<br>$shift</td>"; 

        $day_count++; 

 

        if ($day_count % 7 == 0) $html .= '</tr><tr>'; 

    } 

 

    while ($day_count % 7 != 0) { 

        $html .= '<td></td>'; 

        $day_count++; 

    } 

 

    $html .= '</tr></table>'; 

    return $html; 

} 