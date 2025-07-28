<?php
require 'db.php'; // Includes PDO setup from earlier

$users = [
    [
        'username' => 'teacher1',
        'password' => 'teach123',
        'role'     => 'teacher',
        'name'     => 'Prof. Sharma',
        'enr_no'   => 'TCH001',
        'roll_call'=> 'T101'
    ],
    [
        'username' => 'student1',
        'password' => 'stud123',
        'role'     => 'student',
        'name'     => 'Ravi Kumar',
        'enr_no'   => 'ENR2025A001',
        'roll_call'=> 'R01'
    ]
];

foreach ($users as $u) {
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role, name, enr_no, roll_call) VALUES (?, ?, ?, ?, ?, ?)");
    $hashedPass = password_hash($u['password'], PASSWORD_DEFAULT);
    $stmt->execute([$u['username'], $hashedPass, $u['role'], $u['name'], $u['enr_no'], $u['roll_call']]);
}

echo "Users inserted successfully.";
