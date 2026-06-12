<?php
// =============================================
//   IST LMS - Student Auth (Login + Signup)
//   POST /backend/student_auth.php
//   Body: { "action": "login"|"signup", ... }
// =============================================

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['action'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing action"]);
    exit;
}

// ── STUDENT LOGIN ─────────────────────────────
if ($data['action'] === 'login') {
    if (empty($data['student_id']) || empty($data['password'])) {
        echo json_encode(["success" => false, "message" => "Student ID and password required"]);
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM students WHERE student_id = ?");
    $stmt->execute([$data['student_id']]);
    $student = $stmt->fetch();

    if ($student && password_verify($data['password'], $student['password'])) {
        echo json_encode([
            "success"    => true,
            "message"    => "Login successful",
            "student_id" => $student['student_id'],
            "name"       => $student['full_name'],
            "department" => $student['department']
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid Student ID or password"]);
    }
}

// ── STUDENT SIGNUP ────────────────────────────
elseif ($data['action'] === 'signup') {
    if (empty($data['full_name']) || empty($data['student_id']) || empty($data['password'])) {
        echo json_encode(["success" => false, "message" => "All fields are required"]);
        exit;
    }

    // Check if student_id already exists
    $check = $pdo->prepare("SELECT student_id FROM students WHERE student_id = ?");
    $check->execute([$data['student_id']]);
    if ($check->fetch()) {
        echo json_encode(["success" => false, "message" => "Student ID already registered"]);
        exit;
    }

    $hashed = password_hash($data['password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO students (student_id, full_name, password) VALUES (?, ?, ?)");
    $stmt->execute([$data['student_id'], $data['full_name'], $hashed]);

    echo json_encode(["success" => true, "message" => "Student registered successfully"]);
}

else {
    echo json_encode(["error" => "Unknown action"]);
}
?>
