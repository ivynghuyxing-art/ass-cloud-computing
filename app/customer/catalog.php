
<?php 
require_once '../_base.php'; 
$title = 'Book Catalog'; 
$_title = ''; 
include 'customer_header.php'; 

// Get all categories for filter
$categories = $_db->query("
    SELECT * 
    FROM category 
    ORDER BY category_name
")->fetchAll();

// Get books
$stmt = $_db->prepare("
    SELECT  
        bk.book_id, 
        bk.title, 
        bk.author, 
        bk.category_id, 
        bk.available_quantity, 
        bk.book_photo, 
        c.category_name 
    FROM book bk 
    LEFT JOIN category c ON bk.category_id = c.category_id 
    ORDER BY bk.title
");
$stmt->execute();
$books = $stmt->fetchAll();
?>

<main class="catalog-page">

    <!-- ===== PAGE HEADER ===== -->
    <div class="page-header"> 
        <h1>📚 Book Catalog</h1> 
        <p>Explore our complete collection of books</p> 
    </div> 

    <!-- ===== BOOKS GRID ===== -->
    <?php if (empty($books)): ?>

        <div class="empty-state" style="text-align:center; padding:60px 20px;"> 
            <span style="font-size:48px;">📖</span> 
            <h2>No books found</h2> 
            <p>No books are currently available in the catalog.</p>
            <a href="/customer/catalog.php" class="btn-browse">
                Browse All Books →
            </a> 
        </div> 

    <?php else: ?>

        <div class="results-info"> 
            <p>Showing <?= count($books) ?> books</p> 
        </div> 

        <div class="book-grid"> 

            <?php foreach ($books as $book): ?> 

                <div class="book-card"> 

                    <img 
                        src="/admin/book_photo/<?= encode($book->book_photo ?: 'default.png') ?>" 
                        alt="<?= encode($book->title) ?>"
                    > 

                   <div class="book-info">

    <h3 class="book-title">
        <?= encode($book->title) ?>
    </h3>

    <p class="book-author">
        by <?= encode($book->author) ?>
    </p>

    <p class="book-stock <?= $book->available_quantity > 0 ? 'in-stock' : 'out-of-stock' ?>">
        <?= $book->available_quantity > 0 ? '✅ In Stock' : '❌ Out of Stock' ?>
    </p>

</div>


<div class="book-actions">

    <a href="/customer/book_details.php?id=<?= $book->book_id ?>"
       class="btn-view">
        View Details
    </a>

    <?php if ($book->available_quantity > 0): ?>

        <form method="post"
              action="/customer/borrow.php"
              style="width:100%;">

            <input type="hidden"
                   name="book_id"
                   value="<?= $book->book_id ?>">

            <input type="hidden"
                   name="quantity"
                   value="1">

            <button type="submit"
                    class="btn-cart">
                Borrow
            </button>

        </form>

    <?php endif; ?>

</div>
                    </div> 

                    <a 
                        href="/customer/book_details.php?id=<?= $book->book_id ?>" 
                        class="book-card-link"
                    ></a> 

                </div> 

            <?php endforeach; ?> 

        </div> 

    <?php endif; ?> 

</main> 

<?php include '../footer.php'; ?>
```
