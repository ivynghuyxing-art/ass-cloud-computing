<?php
require_once '../_base.php';

$title = 'Categories';
$_title = '';

include 'customer_header.php';

$category_id = (int)req('id');

// Get all categories
$categories = $_db->query("
    SELECT *
    FROM category
    ORDER BY category_name
")->fetchAll();

// Get books for selected category
$books = [];
if ($category_id > 0) {
    $stmt = $_db->prepare("
        SELECT 
            book_id,
            title,
            author,
            category_id,
            available_quantity,
            book_photo
        FROM book
        WHERE category_id = ?
        ORDER BY title
    ");
    $stmt->execute([$category_id]);
    $books = $stmt->fetchAll();
}
?>

<div class="category-page">

    <!-- ===== PAGE HEADER ===== -->
    <div class="page-header">
        <h1>📚 Categories</h1>
        <p>Browse our collection by genre and find your next great read</p>
    </div>

    <!-- ===== CATEGORY GRID ===== -->
    <div class="category-grid-wrapper">
        <h2 class="section-title">All Categories</h2>
        <div class="category-grid">
            <?php foreach ($categories as $category): ?>
                <a href="?id=<?= $category->category_id ?>" 
                   class="category-card <?= $category_id == $category->category_id ? 'active' : '' ?>">
                    <div class="category-icon">
                        <?php
                        // Different icons for different categories
                        $icons = ['📖', '🔬', '📚', '👶', '💻', '🎨', '🧠', '🌍', '📜', '⚡', '🎭', '🏛️'];
                        $icon_index = $category->category_id % count($icons);
                        echo $icons[$icon_index];
                        ?>
                    </div>
                    <h3><?= encode($category->category_name) ?></h3>
                    <?php
                    // Count books in this category
                    $count_stmt = $_db->prepare("SELECT COUNT(*) FROM book WHERE category_id = ?");
                    $count_stmt->execute([$category->category_id]);
                    $count = $count_stmt->fetchColumn();
                    ?>
                    <span class="book-count"><?= $count ?> book<?= $count > 1 ? 's' : '' ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ===== BOOKS IN SELECTED CATEGORY ===== -->
    <?php if ($category_id > 0): ?>
        <div class="category-books-section">
            <?php
            // Get category name
            $cat_stmt = $_db->prepare("SELECT category_name FROM category WHERE category_id = ?");
            $cat_stmt->execute([$category_id]);
            $cat = $cat_stmt->fetch();
            ?>
            <h2 class="section-title">
                Books in "<?= encode($cat->category_name ?? 'Category') ?>"
            </h2>
            
            <?php if (empty($books)): ?>
                <div class="empty-state" style="text-align:center; padding:40px 20px;">
                    <span style="font-size:40px;">📭</span>
                    <p style="color:#8D6E63; margin-top:10px;">No books available in this category yet.</p>
                </div>
            <?php else: ?>
                <div class="book-grid">
                    <?php foreach ($books as $book): ?>
                        <div class="book-card">
                                <img src="/admin/book_photo/<?= encode($book->book_photo ?: 'default.png') ?>" 
                                     alt="<?= encode($book->title) ?>">
                           
                            <div class="book-info">
                                <h3 class="book-title"><?= encode($book->title) ?></h3>
                                <p class="book-author">by <?= encode($book->author) ?></p>
                                <p class="book-stock <?= $book->available_quantity > 0 ? 'in-stock' : 'out-of-stock' ?>">
                                    <?= $book->available_quantity > 0 ? '✅ In Stock' : '❌ Out of Stock' ?>
                                </p>
                            </div>
                            <div class="book-actions">
                                <a href="/customer/book_details.php?id=<?= $book->book_id ?>" class="btn-view">
                                    View Details
                                </a>
                                <?php if ($book->available_quantity > 0): ?>
                                    <form method="post" action="/customer/borrow.php" style="width:100%;">
                                        <input type="hidden" name="book_id" value="<?= $book->book_id ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn-cart">Borrow</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <!-- Show all books if no category selected -->
        <?php
        $all_books = $_db->query("
            SELECT 
                book_id,
                title,
                author,
                available_quantity,
                book_photo
            FROM book ORDER BY title LIMIT 8
        ")->fetchAll();
        ?>
        <?php if (!empty($all_books)): ?>
            <div class="category-books-section">
                <h2 class="section-title">📖 Featured Books</h2>
                <div class="book-grid">
                    <?php foreach ($all_books as $book): ?>
                        <div class="book-card">
                              <a href="/customer/book_details.php?id=<?= $book->book_id ?>">
                                <img src="/admin/book_photo/<?= encode($book->book_photo ?: 'default.png') ?>" 
                                    alt="<?= encode($book->title) ?>">
                            </a>
                                 
                            <div class="book-info">
                                <h3 class="book-title"><?= encode($book->title) ?></h3>
                                <p class="book-author">by <?= encode($book->author) ?></p>
                                <p class="book-stock <?= $book->available_quantity > 0 ? 'in-stock' : 'out-of-stock' ?>">
                                    <?= $book->available_quantity > 0 ? '✅ In Stock' : '❌ Out of Stock' ?>
                                </p>
                            </div>
                            <div class="book-actions">
                                <a href="/customer/book_details.php?id=<?= $book->book_id ?>" class="btn-view">
                                    View Details
                                </a>
                                <?php if ($book->available_quantity > 0): ?>
                                    <form method="post" action="/customer/borrow.php" style="width:100%;">
                                        <input type="hidden" name="book_id" value="<?= $book->book_id ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn-cart">Borrow</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                        <!-- ===== VIEW ALL ===== -->
                    <div class="featured-view-all">

                        <a href="/customer/catalog.php" class="view-all-btn">
                            View All →
                        </a>

                    </div>
                </div>
        <?php endif; ?>
    <?php endif; ?>

</div>
</main>

<?php include '../footer.php'; ?>