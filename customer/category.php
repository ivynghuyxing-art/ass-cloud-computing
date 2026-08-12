<?php
require_once '../_base.php';

$title = 'Categories';
$_title = '';

include 'customer_header.php';
?>

<main class="category-page">

    <!-- PAGE HEADER -->
    <div class="page-header">
        <h1>📚 Categories</h1>
        <p>Browse our collection by genre and find your next great read</p>
    </div>

<?php
$categories = $_db->query("
    SELECT *
    FROM category
    ORDER BY category_name
")->fetchAll();
?>

<div class="category-grid">

    <?php foreach ($categories as $category): ?>

        <a href="category.php?id=<?= $category->category_id ?>"
           class="category-card">

            <h3>
                <?= encode($category->category_name) ?>
            </h3>

        </a>

    <?php endforeach; ?>

</div>


<?php

$id = get('id');

if ($id) {

    $stmt = $_db->prepare("
        SELECT *
        FROM book
        WHERE category_id = ?
        ORDER BY title
    ");

    $stmt->execute([$id]);

    $books = $stmt->fetchAll();

?>

    <section class="category-section">

        <?php foreach ($books as $book): ?>

            <div class="product-card">

                <?php if ($book->photo): ?>
                    <img src="/images/<?= encode($book->photo) ?>"
                         alt="<?= encode($book->title) ?>">
                <?php endif; ?>

                <h3>
                    <?= encode($book->title) ?>
                </h3>

                <p>
                    Author: <?= encode($book->author) ?>
                </p>

            </div>

        <?php endforeach; ?>

    </section>

<?php } ?>

</main>