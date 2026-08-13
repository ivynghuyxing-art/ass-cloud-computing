<?php
require_once '../_base.php';
$title = 'New Arrivals';
$_title = '';
include 'customer_header.php';

$stmt = $_db->query("
    SELECT 
        book_id,
        title,
        author,
        publisher,
        publish_year,
        category_id,
        quantity,
        available_quantity,
        photo
    FROM book
    ORDER BY book_id DESC
");
$new_arrivals = $stmt->fetchAll();
?>

<main class="new-arrivals-page">

    <!-- ===== PAGE HEADER ===== -->
    <div class="page-header">
        <h1>✨ New Arrivals</h1>
        <p>Discover the latest books added to our collection</p>
    </div>

    <!-- ===== BOOK GRID ===== -->
    <?php if (empty($new_arrivals)): ?>
        <div class="empty-state" style="text-align:center;">
            <span class="empty-icon">📭</span>
            <h2>No new arrivals yet</h2>
            <p>Check back soon for fresh books!</p>
        </div>
    <?php else: ?>
        <div class="book-grid">
            <?php foreach ($new_arrivals as $book): ?>
                <div class="book-card">
                    <img src="/photo/<?= encode($book->photo ?: 'default.png') ?>" 
                         alt="<?= encode($book->title) ?>">
                    
                    <div class="book-info">
                        <h3 class="book-title"><?= encode($book->title) ?></h3>
                        <p class="book-author">by <?= encode($book->author) ?></p>
                        
                        <?php if ($book->category_id): ?>
                            <?php
                            $cat_stmt = $_db->prepare("SELECT category_name FROM category WHERE category_id = ?");
                            $cat_stmt->execute([$book->category_id]);
                            $cat = $cat_stmt->fetch();
                            ?>
                            <?php if ($cat): ?>
                                <span class="book-category"><?= encode($cat->category_name) ?></span>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <p class="book-stock <?= $book->available_quantity > 0 ? 'in-stock' : 'out-of-stock' ?>">
                            <?= $book->available_quantity > 0 ? '✅ In Stock' : '❌ Out of Stock' ?>
                        </p>
                    </div>
                    
                    <div class="book-actions">
                        <a href="/customer/book_details.php?id=<?= $book->book_id ?>" class="btn-view">
                            View Details
                        </a>
                        <?php if ($book->available_quantity > 0): ?>
                            <form method="post" action="/customer/cart_add.php" style="width:100%;">
                                <input type="hidden" name="book_id" value="<?= $book->book_id ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn-cart">🛒 Add to Cart</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</main>

<?php include '../footer.php'; ?>