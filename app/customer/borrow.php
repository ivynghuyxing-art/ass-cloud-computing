<?php
require_once '../_base.php';

// Check if user is logged in
if (!$_user) {
    temp('error', 'Please login first to borrow books');
    redirect('/login.php');
}

$user_id = $_user->user_id;

// Get book ID from POST or GET
$book_id = (int)(post('book_id') ?: get('book_id'));

if (!$book_id) {
    temp('error', 'Book not found');
    redirect('/customer/catalog.php');
}

// Get book information
$book_stmt = $_db->prepare("SELECT * FROM book WHERE book_id = ?");
$book_stmt->execute([$book_id]);
$book = $book_stmt->fetch();

if (!$book) {
    temp('error', 'Book not found');
    redirect('/customer/catalog.php');
}

// Check if book is available
if ($book->available_quantity <= 0) {
    temp('error', 'This book is currently unavailable');
    redirect('/customer/book_details.php?id=' . $book_id);
}

// Check if user already borrowed this book (not returned yet)
$check_stmt = $_db->prepare("
    SELECT COUNT(*) FROM borrowing 
    WHERE user_id = ? AND book_id = ? AND status != 'returned'
");
$check_stmt->execute([$user_id, $book_id]);
$existing_count = $check_stmt->fetchColumn();

if ($existing_count > 0) {
    temp('error', 'You have already borrowed this book. Please return it before borrowing again.');
    redirect('/customer/book_details.php?id=' . $book_id);
}

// Borrow settings
$borrow_period = 14; // days
$borrow_date = time();
$due_date = $borrow_date + ($borrow_period * 86400); // Add 14 days

// Insert borrowing record
$borrow_stmt = $_db->prepare("
    INSERT INTO borrowing (user_id, book_id, borrow_date, due_date, return_date, status)
    VALUES (?, ?, ?, ?, 0, 'borrowed')
");

$success = $borrow_stmt->execute([$user_id, $book_id, $borrow_date, $due_date]);

if ($success) {
    // Update book available quantity
    $update_stmt = $_db->prepare("
        UPDATE book 
        SET available_quantity = available_quantity - 1 
        WHERE book_id = ?
    ");
    $update_stmt->execute([$book_id]);
    
    temp('info', 'Book borrowed successfully! Due date: ' . date('Y-m-d', $due_date));
    redirect('/customer/borrowing_history.php');
} else {
    temp('error', 'Failed to borrow book. Please try again.');
    redirect('/customer/book_details.php?id=' . $book_id);
}
?>
