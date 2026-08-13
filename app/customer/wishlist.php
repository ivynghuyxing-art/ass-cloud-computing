<?php
require_once '../_base.php';
$title = 'My Wishlist';
$_title = '';
include 'customer_header.php';

if (!$_user) {
    redirect('/login.php');
}

$user_id = $_user->user_id;

// Get wishlist items
$stm = $_db->prepare("
    SELECT 
        w.wishlist_id,
        w.book_id,
        w.created_at,
        bk.title,
        bk.author,
        bk.book_photo,
        bk.available_quantity
    FROM wishlist w
    JOIN book bk ON w.book_id = bk.book_id
    WHERE w.user_id = ?
    ORDER BY w.created_at DESC
");
$stm->execute([$user_id]);
$wishlist = $stm->fetchAll();

// Handle remove from wishlist
if (is_post() && post('action') === 'remove') {
    $wishlist_id = post('wishlist_id');
    $stm = $_db->prepare("DELETE FROM wishlist WHERE wishlist_id = ? AND user_id = ?");
    $stm->execute([$wishlist_id, $user_id]);
    temp('info', 'Book removed from wishlist');
    redirect('/customer/wishlist.php');
}

// Handle add to cart from wishlist
if (is_post() && post('action') === 'add_to_cart') {
    $book_id = post('book_id');
    
    // Check if book is available
    $stm = $_db->prepare("SELECT available_quantity FROM book WHERE book_id = ?");
    $stm->execute([$book_id]);
    $book = $stm->fetch();
    
    if ($book && $book->available_quantity > 0) {
        temp('info', 'Book added to cart!');
    } else {
        temp('info', 'Book is currently unavailable');
    }
    redirect('/customer/wishlist.php');
}
?>

<main class="wishlist-page">
    
    <!-- ===== Page Header ===== -->
    <div class="page-header">
        <h1>❤️ My Wishlist</h1>
        <p>Books you've saved for later</p>
    </div>

    <?php if (empty($wishlist)): ?>
        <!-- ===== Empty State ===== -->
        <div class="empty-state" style="text-align:center;">
            <span class="empty-icon">📚</span>
            <h2 style="text-align:center;">Your wishlist is empty</h2>
            <p style="text-align:center;">Start adding books you'd like to read!</p>
            <a href="/customer/catalog.php" class="btn-browse">Browse Books →</a>
        </div>
    <?php else: ?>
        <!-- ===== Wishlist Grid ===== -->
        <div class="wishlist-grid">
            <?php foreach ($wishlist as $item): ?>
                <div class="wishlist-card">
                    <img src="/admin/book_photo/<?= encode($item->book_photo ?: 'default.png') ?>" 
                         alt="<?= encode($item->title) ?>">
                    
                    <div class="wishlist-info">
                        <h3 class="wishlist-name"><?= encode($item->title) ?></h3>
                        <p class="wishlist-author">by <?= encode($item->author) ?></p>
                        <p class="wishlist-stock <?= $item->available_quantity > 0 ? 'in-stock' : 'out-of-stock' ?>">
                            <?= $item->available_quantity > 0 ? '✅ In Stock' : '❌ Out of Stock' ?>
                        </p>
                    </div>
                    
                    <div class="wishlist-actions">
                        <a href="/customer/book_details.php?id=<?= $item->book_id ?>" class="btn-view">View Details</a>
                        
                        <?php if ($item->available_quantity > 0): ?>
                            <form method="post" style="width:100%;">
                                <input type="hidden" name="action" value="add_to_cart">
                                <input type="hidden" name="book_id" value="<?= $item->book_id ?>">
                                <button type="submit" class="btn-cart">🛒 Add to Cart</button>
                            </form>
                        <?php endif; ?>
                        
                        <form method="post" style="width:100%;">
                            <input type="hidden" name="action" value="remove">
                            <input type="hidden" name="wishlist_id" value="<?= $item->wishlist_id ?>">
                            <button type="submit" class="btn-remove" onclick="return confirm('Remove this book from wishlist?')">
                                🗑️ Remove
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</main>

<?php include '../footer.php'; ?>