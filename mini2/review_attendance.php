<?php
session_start();
require 'db.php';

$timetable_id = $_SESSION['timetable_id'];
$recognized = $_SESSION['recognized'] ?? [];
$labeled = json_decode($_POST['labeled'], true);

// merge labeled
foreach ($labeled as $val) {
    if ($val) $recognized[] = $val;
}

// get full list
$students = $pdo->query("SELECT enr_no, name FROM users WHERE role='student'")->fetchAll();
$recognized = array_unique($recognized);
?>

<form method="POST" action="submit_attendance.php">
  <input type="hidden" name="timetable_id" value="<?= $timetable_id ?>">
  <table border="1" cellpadding="5">
    <?php foreach ($students as $s):
      $present = in_array($s['enr_no'], $recognized);
      $checked = $present ? "checked" : "";
      $color = $present ? "style='background-color:lightgreen'" : "style='background-color:#fdd'";
    ?>
    <tr <?= $color ?>>
      <td><input type="checkbox" name="present[]" value="<?= $s['enr_no'] ?>" <?= $checked ?>></td>
      <td><?= $s['enr_no'] ?> - <?= $s['name'] ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
  <input type="hidden" name="labeled_raw" value='<?= json_encode($labeled) ?>'> 
  <button type="submit">Submit Attendance</button>
</form>
