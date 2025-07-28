<?php
session_start();
require 'db.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['enr_no'] = $user['enr_no'];
    $_SESSION['roll_call'] = $user['roll_call'];

    $redirect = $user['role'] === 'teacher' ? 'teacher_home.php' : 'student_home.php';
    echo json_encode(['status' => 'success', 'redirect' => $redirect]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid credentials']);
}
