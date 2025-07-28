<?php
require 'db.php';
session_start();

// Handle AJAX POST submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $day = $_POST['day'] ?? '';
    $from = $_POST['time_from'] ?? '';
    $to = $_POST['time_to'] ?? '';
    $subject_code = $_POST['subject_code'] ?? '';

    if (!$day || !$from || !$to || !$subject_code) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
        exit;
    }

    if ($from >= $to) {
        echo json_encode(['status' => 'error', 'message' => 'End time must be after start time']);
        exit;
    }

    // Check for time overlap
    $stmt = $pdo->prepare("SELECT * FROM timetable WHERE day = ? AND (
        (time_from < ? AND time_to > ?) OR
        (time_from < ? AND time_to > ?) OR
        (time_from >= ? AND time_to <= ?)
    )");

    $stmt->execute([$day, $to, $to, $from, $from, $from, $to]);
    $conflict = $stmt->fetch();

    if ($conflict) {
        echo json_encode(['status' => 'error', 'message' => 'Time overlaps with another subject']);
        exit;
    }

    // Insert
    $insert = $pdo->prepare("INSERT INTO timetable (day, time_from, time_to, subject_code) VALUES (?, ?, ?, ?)");
    $insert->execute([$day, $from, $to, $subject_code]);

    echo json_encode(['status' => 'success']);
    exit;
}
?>

<!-- HTML UI -->
<form id="timetableForm">
  <select name="day" required>
    <option value="">Day</option>
    <option>Monday</option>
    <option>Tuesday</option>
    <option>Wednesday</option>
    <option>Thursday</option>
    <option>Friday</option>
    <option>Saturday</option>
  </select>
  <input type="time" name="time_from" required>
  <input type="time" name="time_to" required>

  <!-- Subject Dropdown (from DB) -->
  <select name="subject_code" required>
    <option value="">Subject</option>
    <?php
    $subjects = $pdo->query("SELECT code FROM subjects ORDER BY code")->fetchAll();
    foreach ($subjects as $sub) {
        echo "<option value='{$sub['code']}'>{$sub['code']}</option>";
    }
    ?>
  </select>

  <button type="submit">Add</button>
  <span id="msg"></span>
</form>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
  $('#timetableForm').on('submit', function(e) {
    e.preventDefault();
    $.post('timetable.php', $(this).serialize(), function(response) {
      if (response.status === 'success') {
        location.reload();
      } else {
        $('#msg').text(response.message);
      }
    }, 'json');
  });
</script>

<!-- Timetable Display -->
<table border="1" cellpadding="5" cellspacing="0">
  <tr><th>Day</th><th>From</th><th>To</th><th>Subject</th></tr>
  <?php
  $query = "
    SELECT t.day, t.time_from, t.time_to, s.code
    FROM timetable t
    JOIN subjects s ON t.subject_code = s.code
    ORDER BY FIELD(t.day,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'), t.time_from
  ";
  $rows = $pdo->query($query)->fetchAll();

  foreach ($rows as $r) {
    echo "<tr>
      <td>{$r['day']}</td>
      <td>{$r['time_from']}</td>
      <td>{$r['time_to']}</td>
      <td>{$r['code']}</td>
    </tr>";
  }
  ?>
</table>
