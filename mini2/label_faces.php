<?php
session_start();
require 'db.php';
print_r($_SESSION);
$imgs = $_SESSION['unknown_imgs'] ?? [];
$recognized = $_SESSION['recognized'] ?? [];
$timetable_id = $_SESSION['timetable_id'] ?? 0;

if (!$imgs && $recognized) {
    header("Location: review_attendance.php");
    exit;
}
?>

<img id="faceImg" src="http://localhost/mini/<?= $imgs[0] ?>" width="200"><br>
<select id="labelSelect">
  <option value="">-- Select Student --</option>
  <?php
  $students = $pdo->query("SELECT enr_no, name FROM users WHERE role='student'")->fetchAll();
  foreach ($students as $s) {
    if(!in_array($s['enr_no'] , $recognized))
      echo "<option value='{$s['enr_no']}'>{$s['enr_no']} - {$s['name']}</option>";
  }
  ?>
</select>
<br>
<button onclick="labelFace()">Mark</button>
<button onclick="skipFace()">Skip</button>

<form id="labelForm" method="POST" action="review_attendance.php">
  <input type="hidden" name="labeled" id="labeledData">
</form>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
let images = <?= json_encode($imgs) ?>;
let current = 0;
let labeled = {};
function labelFace() {
    const val = $('#labelSelect').val();
    if (!val) return alert("Select a student");
    labeled[images[current]] = val;
    nextFace();
}
function skipFace() {
    labeled[images[current]] = null;
    nextFace();
}
function nextFace() {
    current++;
    if (current >= images.length) {
        $('#labeledData').val(JSON.stringify(labeled));
        $('#labelForm').submit();
    } else {
        $('#faceImg').attr('src', images[current]);
        $('#labelSelect').val('');
    }
}
</script>
