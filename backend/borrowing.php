<?php
// =============================================
//   IST LMS - Borrowings API
//   GET  /backend/borrowings.php              → all active borrowings
//   GET  /backend/borrowings.php?student=ID   → student's borrowings
//   GET  /backend/borrowings.php?history=ID   → student's full history
//   POST /backend/borrowings.php              → assign or return
// =============================================

require 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

// ── GET: fetch borrowings ─────────────────────
if ($method === 'GET') {

    // Student-specific current books
    if (isset($_GET['student'])) {
        $sid = $_GET['student'];
        $stmt = $pdo->prepare("
            SELECT br.borrow_id, bk.title, bk.author, bk.category,
                   br.borrow_date, br.due_date, br.status
            FROM borrowings br
            JOIN books bk ON br.book_id = bk.book_id
            WHERE br.student_id = ? AND br.status = 'issued'
            ORDER BY br.borrow_date DESC
        ");
        $stmt->execute([$sid]);
        echo json_encode($stmt->fetchAll());
    }

    // Student full history
    elseif (isset($_GET['history'])) {
        $sid = $_GET['history'];
        $stmt = $pdo->prepare("
            SELECT br.borrow_id, bk.title, br.borrow_date, br.due_date,
                   br.return_date, br.status
            FROM borrowings br
            JOIN books bk ON br.book_id = bk.book_id
            WHERE br.student_id = ?
            ORDER BY br.borrow_date DESC
        ");
        $stmt->execute([$sid]);
        echo json_encode($stmt->fetchAll());
    }

    // All currently issued (for admin dashboard table)
    else {
        $stmt = $pdo->query("
            SELECT br.borrow_id, bk.title, bk.author, bk.category,
                   s.full_name AS student_name, br.student_id,
                   br.borrow_date, br.due_date, br.status
            FROM borrowings br
            JOIN books bk ON br.book_id = bk.book_id
            JOIN students s ON br.student_id = s.student_id
            WHERE br.status = 'issued'
            ORDER BY br.borrow_date DESC
        ");
        echo json_encode($stmt->fetchAll());
    }
}

// ── POST: assign or return ────────────────────
elseif ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    // ── ASSIGN BOOK ───────────────────────────
    if ($data['action'] === 'assign') {
        if (empty($data['student_id']) || empty($data['book_id'])) {
            echo json_encode(["success" => false, "message" => "Student ID and Book ID required"]);
            exit;
        }

        // Check student exists
        $cs = $pdo->prepare("SELECT student_id FROM students WHERE student_id = ?");
        $cs->execute([$data['student_id']]);
        if (!$cs->fetch()) {
            echo json_encode(["success" => false, "message" => "Student ID not found"]);
            exit;
        }

        // Check book availability
        $cb = $pdo->prepare("SELECT available_copies FROM books WHERE book_id = ?");
        $cb->execute([$data['book_id']]);
        $book = $cb->fetch();
        if (!$book) {
            echo json_encode(["success" => false, "message" => "Book ID not found"]);
            exit;
        }
        if ($book['available_copies'] < 1) {
            echo json_encode(["success" => false, "message" => "No copies available"]);
            exit;
        }

        // Check if already borrowed
        $dup = $pdo->prepare("SELECT borrow_id FROM borrowings WHERE student_id=? AND book_id=? AND status='issued'");
        $dup->execute([$data['student_id'], $data['book_id']]);
        if ($dup->fetch()) {
            echo json_encode(["success" => false, "message" => "Student already has this book"]);
            exit;
        }

        // Insert borrowing (due in 14 days)
        $due = date('Y-m-d', strtotime('+14 days'));
        $ins = $pdo->prepare("INSERT INTO borrowings (student_id, book_id, due_date) VALUES (?,?,?)");
        $ins->execute([$data['student_id'], $data['book_id'], $due]);

        // Decrease available copies
        $pdo->prepare("UPDATE books SET available_copies = available_copies - 1 WHERE book_id = ?")
            ->execute([$data['book_id']]);

        echo json_encode(["success" => true, "message" => "Book assigned! Due: $due"]);
    }

    // ── RETURN BOOK ───────────────────────────
    elseif ($data['action'] === 'return') {
        if (empty($data['student_id']) || empty($data['book_id'])) {
            echo json_encode(["success" => false, "message" => "Student ID and Book ID required"]);
            exit;
        }

        // Find active borrowing
        $find = $pdo->prepare("SELECT borrow_id FROM borrowings WHERE student_id=? AND book_id=? AND status='issued'");
        $find->execute([$data['student_id'], $data['book_id']]);
        $row = $find->fetch();

        if (!$row) {
            echo json_encode(["success" => false, "message" => "No active borrow found for this combination"]);
            exit;
        }

        // Mark returned
        $pdo->prepare("UPDATE borrowings SET return_date = CURDATE(), status = 'returned' WHERE borrow_id = ?")
            ->execute([$row['borrow_id']]);

        // Increase available copies
        $pdo->prepare("UPDATE books SET available_copies = available_copies + 1 WHERE book_id = ?")
            ->execute([$data['book_id']]);

        echo json_encode(["success" => true, "message" => "Book returned successfully"]);
    }

    else {
        echo json_encode(["error" => "Unknown action"]);
    }
}

else {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
}
?>
