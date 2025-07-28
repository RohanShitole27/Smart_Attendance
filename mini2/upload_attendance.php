<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    set_time_limit(0);
    $timetable_id = $_POST['timetable_id'];
    $timestamp = date('Ymd_His');
    $folder = "uploads/attendance/{$timestamp}_{$timetable_id}";
    mkdir($folder, 0777, true);

    if ($_FILES['photo1']['error'] === 0) {
        move_uploaded_file($_FILES['photo1']['tmp_name'], "$folder/img1.jpg");
    }

    if ($_FILES['photo2']['error'] === 0) {
        move_uploaded_file($_FILES['photo2']['tmp_name'], "$folder/img2.jpg");
    }

    // // Call Python script
    // $output = shell_exec("python3 detect_faces.py '$folder'");
    // $result = json_decode($output, true); // expected keys: recognized, unknown_imgs

    $img_path = "$folder/img1.jpg";

    $data = ['image_path' => $img_path];
    $ch = curl_init('http://localhost:5000/detect_faces');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    //  print_r($result);
    //  print_r($result['results']);

    foreach ($result['results'] as $face) {
    //   print_r($face);
        if ($face['identity'] !== 'Unknown') {
            $recognized[] =  $face['identity'];
        } else {
            $unrecognized[] = $face['image_path'];
        }
    }
    // Store in session
    $_SESSION['recognized'] = $recognized; // array of enrollment_ids
    $_SESSION['unknown_imgs'] = $unrecognized; // array of image paths
    $_SESSION['timetable_id'] = $timetable_id;
    
    header("Location: label_faces.php");
    exit;
}
?>


<!-- HTML Form -->
<form method="POST" enctype="multipart/form-data">
  <!-- Timetable Selection -->
  <select name="timetable_id" required>
    <option value="">Select Class</option>
    <?php
    $rows = $pdo->query("SELECT id, day, time_from, time_to FROM timetable")->fetchAll();
    foreach ($rows as $row) {
        echo "<option value='{$row['id']}'>{$row['day']} {$row['time_from']} - {$row['time_to']}</option>";
    }
    ?>
  </select>

  <!-- Photo Inputs (open camera on mobile) -->
  <input type="file" name="photo1" accept="image/*" capture="environment" required>
  <input type="file" name="photo2" accept="image/*" capture="environment">

  <button type="submit">Upload & Detect</button>
</form>
