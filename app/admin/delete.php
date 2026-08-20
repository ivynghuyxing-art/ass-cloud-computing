<?php

require_once '../_base.php';

// Make sure only admin can delete books
if (
    !isset($_SESSION['user']) ||
    $_SESSION['user']->role !== 'admin'
) {
    redirect('../login.php');
}


// Get book ID from URL
$id = $_GET['id'] ?? null;


// Check valid ID
if (!$id || !ctype_digit((string)$id)) {

    temp('info', 'Invalid book ID.');

    redirect('admin_panel.php?page=books');
}


// Find the book first
$stm = $_db->prepare(
    'SELECT *
     FROM book
     WHERE book_id = ?'
);

$stm->execute([$id]);

$book = $stm->fetch();


// Book not found
if (!$book) {

    temp('info', 'Book not found.');

    redirect('admin_panel.php?page=books');
}


// Check whether the book has borrowing records
$stm = $_db->prepare(
    'SELECT COUNT(*)
     FROM borrowing
     WHERE book_id = ?'
);

$stm->execute([$id]);

$borrowCount = $stm->fetchColumn();


// Do not delete book with borrowing history
if ($borrowCount > 0) {

    temp(
        'info',
        'Cannot delete this book because it has borrowing records.'
    );

    redirect('admin_panel.php?page=books');
}


// Delete wishlist records related to this book
$stm = $_db->prepare(
    'DELETE FROM wishlist
     WHERE book_id = ?'
);

$stm->execute([$id]);


// Delete the book
$stm = $_db->prepare(
    'DELETE FROM book
     WHERE book_id = ?'
);

$stm->execute([$id]);


// Delete book photo from folder
if (!empty($book->book_photo)) {

    $photo = __DIR__
        . '/book_photo/'
        . $book->book_photo;

    if (is_file($photo)) {
        unlink($photo);
    }
}


// Success message
temp('info', 'Book deleted successfully.');

redirect('admin_panel.php?page=books');