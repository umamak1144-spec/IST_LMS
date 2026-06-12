<?php
// =============================================
//   IST LMS - Admin Auth (Login + Signup)
//   POST /backend/admin_auth.php
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

// ── ADMIN LOGIN ──────────────────────────────
if ($data['action'] === 'login') {
    if (empty($data['email']) || empty($data['password'])) {
        echo json_encode(["success" => false, "message" => "Email and password required"]);
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
    $stmt->execute([$data['email']]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($data['password'], $admin['password'])) {
        echo json_encode([
            "success"  => true,
            "message"  => "Login successful",
            "admin_id" => $admin['admin_id'],
            "name"     => $admin['name']
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid email or password"]);
    }
}

// ── ADMIN SIGNUP ─────────────────────────────
elseif ($data['action'] === 'signup') {
    if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
        echo json_encode(["success" => false, "message" => "All fields are required"]);
        exit;
    }

    // Check if email already exists
    $check = $pdo->prepare("SELECT admin_id FROM admins WHERE email = ?");
    $check->execute([$data['email']]);
    if ($check->fetch()) {
        echo json_encode(["success" => false, "message" => "Email already registered"]);
        exit;
    }

    $hashed = password_hash($data['password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO admins (name, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$data['name'], $data['email'], $hashed]);

    echo json_encode(["success" => true, "message" => "Admin registered successfully"]);
}

else {
    echo json_encode(["error" => "Unknown action"]);
}
?>
