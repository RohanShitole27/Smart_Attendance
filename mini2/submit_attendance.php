<?php
session_start();
require 'db.php';

$timetable_id = $_POST['timetable_id'];
$present = $_POST['present'] ?? [];
$present = array_map('trim', $present);
$all = $pdo->query("SELECT enr_no FROM users WHERE role='student'")->fetchAll(PDO::FETCH_COLUMN);
$now = date('Y-m-d H:i:s');
$day = date('l');

// Save attendance
$stmt = $pdo->prepare("INSERT INTO attendance (datetime, day, timetable_id, enr_no, status) VALUES (?, ?, ?, ?, ?)");

foreach ($all as $enr) {
    $status = in_array($enr, $present) ? 'present' : 'absent';
    $stmt->execute([$now, $day, $timetable_id, $enr, $status]);
}

$labeled = json_decode($_POST['labeled_raw'], true);
$faces_dir = __DIR__ . "/face_db"; // path to your DB folder

foreach ($labeled as $key => $enr) { 
    $enr = $enr;
    $imgPath = $key;

    if (!$enr || !$imgPath || !file_exists($imgPath)) continue;

    $targetDir = $faces_dir . '/' . $enr;
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    // Create a unique filename to avoid overwriting
    $newName = "face_" . date("Ymd_His") . "_" . rand(1000,9999) . ".jpg";
    copy($imgPath, "$targetDir/$newName");
}

echo "Attendance submitted successfully.";
?>
