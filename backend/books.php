<?php
// =============================================
//   IST LMS - Books API
//   GET    /backend/books.php           → all books
//   GET    /backend/books.php?search=x  → search
//   POST   /backend/books.php           → add book
//   DELETE /backend/books.php?id=N      → delete book
// =============================================

require 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

// ── GET: fetch all books or search ───────────
if ($method === 'GET') {
    if (isset($_GET['search']) && $_GET['search'] !== '') {
        $q = '%' . $_GET['search'] . '%';
        $stmt = $pdo->prepare(
            "SELECT * FROM books WHERE title LIKE ? OR author LIKE ? OR category LIKE ?"
        );
        $stmt->execute([$q, $q, $q]);
    } else {
        $stmt = $pdo->query("SELECT * FROM books ORDER BY book_id ASC");
    }
    echo json_encode($stmt->fetchAll());
}

// ── POST: add a new book ──────────────────────
elseif ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (empty($data['title']) || empty($data['author'])) {
        echo json_encode(["success" => false, "message" => "Title and author are required"]);
        exit;
    }

    $copies = intval($data['copies'] ?? 1);
    $stmt = $pdo->prepare(
        "INSERT INTO books (title, author, category, total_copies, available_copies)
         VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->execute([
        $data['title'],
        $data['author'],
        $data['category'] ?? 'General',
        $copies,
        $copies
    ]);
    echo json_encode(["success" => true, "message" => "Book added", "id" => $pdo->lastInsertId()]);
}

// ── DELETE: remove a book ─────────────────────
elseif ($method === 'DELETE') {
    $id = intval($_GET['id'] ?? 0);
    if (!$id) {
        echo json_encode(["success" => false, "message" => "Book ID required"]);
        exit;
    }
    $stmt = $pdo->prepare("DELETE FROM books WHERE book_id = ?");
    $stmt->execute([$id]);
    echo json_encode(["success" => true, "message" => "Book deleted"]);
}

else {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
}
?>
