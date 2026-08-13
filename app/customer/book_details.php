<?php
require_once '../_base.php';

$title = 'Book Details';
$_title = '';
include 'customer_header.php';

// Get book ID
$book_id = (int)get('id');

if (!$book_id) {
    redirect('/customer/catalog.php');
}

// Get book details
$book_stmt = $_db->prepare("
    SELECT 
        b.*,
        c.category_name
    FROM book b
    LEFT JOIN category c ON b.category_id = c.category_id
    WHERE b.book_id = ?
");
$book_stmt->execute([$book_id]);
$book = $book_stmt->fetch();

if (!$book) {
    redirect('/customer/catalog.php');
}

// Check if user already borrowed this book
$user_borrowed = false;
if ($_user) {
    $check_stmt = $_db->prepare("
        SELECT COUNT(*) FROM borrowing 
        WHERE user_id = ? AND book_id = ? AND status != 'returned'
    ");
    $check_stmt->execute([$_user->user_id, $book_id]);
    $user_borrowed = $check_stmt->fetchColumn() > 0;
}

// Get wishlist status
$in_wishlist = false;
if ($_user) {
    $wish_stmt = $_db->prepare("
        SELECT COUNT(*) FROM wishlist 
        WHERE user_id = ? AND book_id = ?
    ");
    $wish_stmt->execute([$_user->user_id, $book_id]);
    $in_wishlist = $wish_stmt->fetchColumn() > 0;
}

// Handle wishlist add/remove
if (is_post() && $_user) {
    $action = post('action');
    if ($action === 'add_wishlist') {
        $insert_stmt = $_db->prepare("
            INSERT IGNORE INTO wishlist (user_id, book_id, created_at)
            VALUES (?, ?, ?)
        ");
        $insert_stmt->execute([$_user->user_id, $book_id, time()]);
        $in_wishlist = true;
    } elseif ($action === 'remove_wishlist') {
        $delete_stmt = $_db->prepare("
            DELETE FROM wishlist WHERE user_id = ? AND book_id = ?
        ");
        $delete_stmt->execute([$_user->user_id, $book_id]);
        $in_wishlist = false;
    }
}
?>

<main class="book-details-page">

    <!-- ===== PAGE HEADER ===== -->
    <div class="page-header">
        <h1>📖 Book Details</h1>
    </div>

    <!-- ===== BOOK DETAILS CONTAINER ===== -->
    <div class="book-details-container">

        <!-- ===== BOOK COVER ===== -->
        <div class="book-cover-section">
            <img 
                src="/admin/book_photo/<?= encode($book->book_photo ?: 'default.png') ?>" 
                alt="<?= encode($book->title) ?>"
                class="book-cover-image"
            >
        </div>

        <!-- ===== BOOK INFO ===== -->
        <div class="book-info-section">

            <!-- Title -->
            <h1 class="book-detail-title">
                <?= encode($book->title) ?>
            </h1>

            <!-- Author -->
            <p class="book-detail-author">
                by <strong><?= encode($book->author) ?></strong>
            </p>

            <!-- Category -->
            <?php if ($book->category_name): ?>
                <div class="book-detail-category">
                    <span class="badge-category">
                        📚 <?= encode($book->category_name) ?>
                    </span>
                </div>
            <?php endif; ?>

            <!-- Publisher & Year -->
            <div class="book-detail-meta">
                <div class="meta-item">
                    <span class="meta-label">Publisher:</span>
                    <span class="meta-value"><?= encode($book->publisher) ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Published:</span>
                    <span class="meta-value"><?= date('Y', strtotime($book->publish_year)) ?></span>
                </div>
            </div>

            <!-- Availability -->
            <div class="book-detail-availability">
                <div class="availability-label">
                    <?php if ($book->available_quantity > 0): ?>
                        <span class="status in-stock">✅ In Stock</span>
                        <span class="quantity"><?= $book->available_quantity ?> copies available</span>
                    <?php else: ?>
                        <span class="status out-of-stock">❌ Out of Stock</span>
                        <span class="quantity">Currently unavailable</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ===== ACTIONS ===== -->
            <div class="book-detail-actions">

                <!-- Borrow Button -->
                <?php if ($book->available_quantity > 0 && !$user_borrowed): ?>
                    <?php if ($_user): ?>
                        <form method="post" action="borrow.php">
                            <input type="hidden" name="book_id" value="<?= $book->book_id ?>">
                            <button type="submit" class="btn-borrow">
                                🎁 Borrow This Book
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="/login.php" class="btn-borrow">
                            🎁 Login to Borrow
                        </a>
                    <?php endif; ?>
                <?php elseif ($user_borrowed): ?>
                    <div class="btn-borrowed">
                        ✓ You're currently borrowing this book
                    </div>
                <?php else: ?>
                    <div class="btn-unavailable">
                        ✗ This book is currently unavailable
                    </div>
                <?php endif; ?>

                <!-- Wishlist Button -->
                <?php if ($_user): ?>
                    <form method="post" style="display: inline;">
                        <input type="hidden" name="action" value="<?= $in_wishlist ? 'remove_wishlist' : 'add_wishlist' ?>">
                        <button type="submit" class="btn-wishlist <?= $in_wishlist ? 'active' : '' ?>">
                            <?= $in_wishlist ? '❤️ Remove from Wishlist' : '🤍 Add to Wishlist' ?>
                        </button>
                    </form>
                <?php else: ?>
                    <a href="/login.php" class="btn-wishlist">
                        🤍 Login to Add Wishlist
                    </a>
                <?php endif; ?>

            </div>

        </div>

    </div>

    <!-- ===== DIVIDER ===== -->
    <hr class="detail-divider">

    <!-- ===== BOOK STATISTICS ===== -->
    <div class="book-statistics">
        <div class="stat-card">
            <div class="stat-value"><?= $book->quantity ?></div>
            <div class="stat-label">Total Copies</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?= $book->available_quantity ?></div>
            <div class="stat-label">Available</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?= $book->quantity - $book->available_quantity ?></div>
            <div class="stat-label">Borrowed</div>
        </div>
    </div>

    <!-- ===== BACK BUTTON ===== -->
    <div class="book-detail-footer">
        <a href="javascript:history.back()" class="btn-back">
            ← Go Back
        </a>
    </div>

</main>

<?php include '../footer.php'; ?>
