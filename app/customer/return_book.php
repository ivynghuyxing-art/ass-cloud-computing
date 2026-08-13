<?php
require_once '../_base.php';

// Check if user is logged in
if (!$_user) {
    temp('error', 'Please login first');
    redirect('/login.php');
}

$user_id = $_user->user_id;

// Get borrowing ID
$borrowing_id = (int)(post('borrowing_id') ?: get('borrowing_id'));

if (!$borrowing_id) {
    temp('error', 'Borrowing record not found');
    redirect('/customer/borrowing_history.php');
}

// Get borrowing record
$borrow_stmt = $_db->prepare("
    SELECT b.*, bk.book_id 
    FROM borrowing b
    JOIN book bk ON b.book_id = bk.book_id
    WHERE b.borrowing_id = ? AND b.user_id = ?
");
$borrow_stmt->execute([$borrowing_id, $user_id]);
$record = $borrow_stmt->fetch();

if (!$record) {
    temp('error', 'Borrowing record not found');
    redirect('/customer/borrowing_history.php');
}

// Check if already returned
if ($record->status === 'returned') {
    temp('error', 'This book has already been returned');
    redirect('/customer/borrowing_history.php');
}

// Update borrowing record
$return_date = time();

// Check if overdue
$is_overdue = $return_date > $record->due_date;
$status = $is_overdue ? 'overdue' : 'returned';

$update_stmt = $_db->prepare("
    UPDATE borrowing 
    SET return_date = ?, status = 'returned'
    WHERE borrowing_id = ?
");

$success = $update_stmt->execute([$return_date, $borrowing_id]);

if ($success) {
    // Update book available quantity
    $book_update = $_db->prepare("
        UPDATE book 
        SET available_quantity = available_quantity + 1 
        WHERE book_id = ?
    ");
    $book_update->execute([$record->book_id]);
    
    // Check if overdue and create fine
    if ($is_overdue) {
        $days_overdue = ceil(($return_date - $record->due_date) / 86400);
        $fine_amount = $days_overdue * 5; // RM5 per day
        
        $fine_stmt = $_db->prepare("
            INSERT INTO fine (borrowing_id, amount, reason, payment_status)
            VALUES (?, ?, ?, 'unpaid')
        ");
        $fine_stmt->execute([$borrowing_id, $fine_amount, "Overdue by $days_overdue days"]);
        
        temp('info', "Book returned successfully! You have an overdue fine of RM" . number_format($fine_amount, 2));
    } else {
        temp('info', 'Book returned successfully!');
    }
    
    redirect('/customer/borrowing_history.php');
} else {
    temp('error', 'Failed to return book. Please try again.');
    redirect('/customer/borrowing_history.php');
}
?>
