<?php
require '_base.php';

include 'customer/customer_header.php';
?>


<!-- ===== HERO BANNER ===== -->
<section class="hero-banner">
    <div class="hero-content">
        <h1>Welcome to Book Nest</h1>
        <p>Your gateway to knowledge, stories, and discovery.</p>
    </div>
</section>


<div class="home-wrap">

    <!-- ===== QUICK LINKS ===== -->
    <section class="quick-links">

        <a href="/customer/category.php" class="quick-card">
            <div class="icon">📚</div>
            <h3>Categories</h3>
            <p>Browse books by genre</p>
        </a>

        <a href="/customer/new_arrivals.php" class="quick-card">
            <div class="icon">✨</div>
            <h3>New Arrivals</h3>
            <p>Books added this week</p>
        </a>

        <a href="/customer/fines.php" class="quick-card">
            <div class="icon">💰</div>
            <h3>My Fines</h3>
            <p>View and pay overdue fines</p>
        </a>

    </section>


    <!-- ===== POPULAR BOOKS ===== -->
    <section>

        <div class="section-header">

            <h2 class="section-title">
                🔥 Popular Books
            </h2>

            <a href="/customer/catalog.php" class="view-all">
                View All →
            </a>

        </div>


        <div class="book-grid">

            <?php
            $stm = $_db->query(
                "SELECT 
                    book_id,
                    title,
                    author,
                    publisher,
                    publish_year,
                    category_id,
                    quantity,
                    available_quantity,
                    book_photo
                FROM book ORDER BY book_id DESC LIMIT 4"
            );

            $popular_books = $stm->fetchAll();
            ?>


            <?php if (empty($popular_books)): ?>

                <p class="empty-msg">
                    No books available yet.
                </p>

            <?php else: ?>

                <?php foreach ($popular_books as $book): ?>

                    <div class="book-card">

                     <a href="/customer/book_details.php?id=<?= $book->book_id ?>">
                        <img
                            src="/admin/book_photo/<?= encode($book->book_photo ?: 'default.png') ?>"
                            alt="<?= encode($book->title) ?>"
                        >
                </a>

                        <div class="info">

                            <h4>
                                <?= encode($book->title) ?>
                            </h4>

                            <p>
                                <?= encode($book->author) ?>
                            </p>

                            <div class="book-actions">
                                <a href="/customer/book_details.php" class="btn-view">
                                    View Details
                                </a>

                            <?php if ($book->available_quantity > 0): ?>
                                <form method="post" action="/customer/borrow.php" style="width:100%;">
                                    <input type="hidden" name="book_id" value="<?= $book->book_id ?>">
                                    <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn-cart">
                                            Borrow
                                        </button>
                                </form>
                        <?php endif; ?>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>

    </section>

</div>
</main>


<?php
include 'footer.php';
?>