<?php

require_once '../_base.php';

$_title = 'Search Results';
$hidePageTitle = true;

include 'customer_header.php';

$keyword = trim($_GET['product_name'] ?? '');

$books = [];

if ($keyword !== '') {

    $stmt = $_db->prepare("
        SELECT
            b.book_id,
            b.title,
            b.author,
            b.publisher,
            b.publish_year,
            b.category_id,
            b.quantity,
            b.available_quantity,
            b.book_photo AS photo,
            c.category_name
        FROM book b
        LEFT JOIN category c
            ON b.category_id = c.category_id
        WHERE
            b.title LIKE ?
            OR b.author LIKE ?
            OR b.publisher LIKE ?
            OR c.category_name LIKE ?
        ORDER BY b.title
    ");

    $search = "%$keyword%";

    $stmt->execute([
        $search,
        $search,
        $search,
        $search
    ]);

    $books = $stmt->fetchAll();
}

?>

<div class="search-page">

    <?php if ($keyword === ''): ?>

        <div class="empty-state">
            <p>Please enter a book name to search.</p>
        </div>

    <?php elseif (empty($books)): ?>

        <div class="empty-state">

            <h2>No books found</h2>

            <p>
                No results found for
                "<strong><?= encode($keyword) ?></strong>"
            </p>

        </div>

    <?php else: ?>

        <div class="results-info">
            Showing <?= count($books) ?> result<?= count($books) !== 1 ? 's' : '' ?>
            for "<strong><?= encode($keyword) ?></strong>"
        </div>

        <div class="book-grid">

            <?php foreach ($books as $book): ?>

                <div class="book-card">

                    <div class="book-image">

                        <?php if (!empty($book->photo)): ?>

                            <img
                                src="/admin/book_photo/<?= encode($book->photo) ?>"
                                alt="<?= encode($book->title) ?>"
                            >

                        <?php else: ?>

                            <div class="no-image">
                                No Image
                            </div>

                        <?php endif; ?>

                    </div>

                    <div class="book-info">

                        <h3>
                            <?= encode($book->title) ?>
                        </h3>

                        <p>
                            <strong>Author:</strong>
                            <?= encode($book->author) ?>
                        </p>

                        <p>
                            <strong>Publisher:</strong>
                            <?= encode($book->publisher) ?>
                        </p>

                        <p>
                            <strong>Category:</strong>
                            <?= encode($book->category_name ?? 'Unknown') ?>
                        </p>

                        <p>
                            <strong>Available:</strong>
                            <?= $book->available_quantity ?>
                            /
                            <?= $book->quantity ?>
                        </p>

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

</main>

<?php include '../footer.php'; ?>
