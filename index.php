<?php
require '_base.php';

$_title = "Home";
include 'customer/customer_header.php';
?>

<!-- ===== HERO BANNER ===== -->
<section class="hero-banner">
    <div class="hero-content">
        <h1>Welcome to Book Nest</h1>
        <p>Your gateway to knowledge, stories, and discovery.</p>
        </div>
    </div>
</section>


<main class="home-wrap">

    <!-- ===== QUICK LINKS ===== -->
    <section class="quick-links">
        <a href="/customer/borrowing_history.php" class="quick-card">
            <div class="icon">📖</div>
            <h3>Borrowing History</h3>
            <p>See everything you've borrowed</p>
        </a>
        <a href="/customer/fines.php" class="quick-card">
            <div class="icon">💰</div>
            <h3>My Fines</h3>
            <p>View and pay overdue fines</p>
        </a>
        <a href="/customer/new_arrivals.php" class="quick-card">
            <div class="icon">✨</div>
            <h3>New Arrivals</h3>
            <p>Books added this week</p>
        </a>
    </section>

    <!-- ===== NEW ARRIVALS ===== -->
    <section>
        <h2 class="section-title">New Arrivals</h2>
        <div class="book-grid">
            <?php
            // Get latest books from database (4 books)
            $stm = $_db->query("SELECT * FROM book ORDER BY created_at DESC LIMIT 4");
            $new_arrivals = $stm->fetchAll();
            ?>
            
            <?php if (empty($new_arrivals)): ?>
                <p class="empty-msg">No new books yet.</p>
            <?php else: ?>
                <?php foreach ($new_arrivals as $book): ?>
                    <div class="book-card">
                        <img src="<?= encode($book->cover_image ?: '/images/placeholder.jpg') ?>"
                             alt="<?= encode($book->title) ?>">
                        <div class="info">
                            <h4><?= encode($book->title) ?></h4>
                            <p><?= encode($book->author) ?></p>
                            <a href="/customer/book_details.php?id=<?= $book->book_id ?>" class="btn-small">View Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- ===== CATEGORIES ===== -->
    <section>
        <h2 class="section-title">Categories</h2>
        <div class="cat-grid">
            <?php
            // Get all categories from database
            $stm = $_db->query("SELECT * FROM category ORDER BY name");
            $categories = $stm->fetchAll();
            ?>
            
            <?php if (empty($categories)): ?>
                <p class="empty-msg">No categories yet.</p>
            <?php else: ?>
                <?php foreach ($categories as $cat): ?>
                    <a href="/customer/category.php?id=<?= (int)$cat->category_id ?>" class="cat-chip">
                        <?= encode($cat->name) ?>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

</main>

<?php
include 'footer.php';
