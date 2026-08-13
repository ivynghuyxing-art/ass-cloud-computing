<?php

require_once __DIR__ . '/../_base.php';

$_err = [];

$title = 'Add Book';


// =====================================
// GET CATEGORIES
// =====================================

$stm = $_db->query(
    'SELECT category_id, category_name
     FROM category
     ORDER BY category_name'
);

$categories = $stm->fetchAll();


// =====================================
// DEFAULT VALUES
// =====================================

$book_title   = '';
$author       = '';
$publisher    = '';
$publish_year = '';
$category_id  = '';
$quantity     = '';


// =====================================
// ADD BOOK
// =====================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $book_title   = trim($_POST['title'] ?? '');
    $author       = trim($_POST['author'] ?? '');
    $publisher    = trim($_POST['publisher'] ?? '');
    $publish_year = trim($_POST['publish_year'] ?? '');
    $category_id  = $_POST['category_id'] ?? '';
    $quantity     = trim($_POST['quantity'] ?? '');

    // Get uploaded photo
    $f = get_file('book_photo');


    // =====================================
    // VALIDATION - TITLE
    // =====================================

    if ($book_title === '') {

        $_err['title'] = 'Book title is required.';

    } elseif (strlen($book_title) > 150) {

        $_err['title'] = 'Maximum 150 characters.';
    }


    // =====================================
    // VALIDATION - AUTHOR
    // =====================================

    if ($author === '') {

        $_err['author'] = 'Author is required.';

    } elseif (strlen($author) > 100) {

        $_err['author'] = 'Maximum 100 characters.';
    }


    // =====================================
    // VALIDATION - PUBLISHER
    // =====================================

    if ($publisher === '') {

        $_err['publisher'] = 'Publisher is required.';

    } elseif (strlen($publisher) > 100) {

        $_err['publisher'] = 'Maximum 100 characters.';
    }


    // =====================================
    // VALIDATION - PUBLISH YEAR
    // =====================================

    if ($publish_year === '') {

        $_err['publish_year'] = 'Publish year is required.';

    } elseif (!ctype_digit($publish_year)) {

        $_err['publish_year'] = 'Publish year must be a number.';

    } elseif (strlen($publish_year) !== 4) {

        $_err['publish_year'] = 'Please enter a 4-digit year.';

    } elseif (
        (int)$publish_year < 1000 ||
        (int)$publish_year > (int)date('Y')
    ) {

        $_err['publish_year'] = 'Invalid publish year.';
    }


    // =====================================
    // VALIDATION - CATEGORY
    // =====================================

    if ($category_id === '') {

        $_err['category_id'] = 'Please select a category.';
    }


    // =====================================
    // VALIDATION - QUANTITY
    // =====================================

    if ($quantity === '') {

        $_err['quantity'] = 'Quantity is required.';

    } elseif (!ctype_digit($quantity)) {

        $_err['quantity'] = 'Quantity must be a number.';

    } elseif ((int)$quantity < 1) {

        $_err['quantity'] = 'Quantity must be at least 1.';
    }


    // =====================================
    // VALIDATION - BOOK PHOTO
    // =====================================

    if (!$f) {

        $_err['book_photo'] = 'Book photo is required.';

    } elseif (!str_starts_with($f->type, 'image/')) {

        $_err['book_photo'] = 'File must be an image.';

    } elseif ($f->size > 2 * 1024 * 1024) {

        $_err['book_photo'] = 'Maximum photo size is 2MB.';
    }


    // =====================================
    // INSERT BOOK
    // =====================================

    if (empty($_err)) {

        $quantity_number = (int)$quantity;

        // New book:
        // all copies are available
        $available_quantity = $quantity_number;


        // Database uses DATE
        // Example:
        // 2022 becomes 2022-01-01
        $publish_date = $publish_year . '-01-01';


        // =====================================
        // SAVE PHOTO
        // =====================================

        $book_photo = save_photo(
            $f,
            __DIR__ . '/book_photo'
        );


        // =====================================
        // INSERT DATABASE
        // =====================================

        $stm = $_db->prepare(
            'INSERT INTO book
            (
                title,
                author,
                publisher,
                publish_year,
                category_id,
                quantity,
                available_quantity,
                book_photo
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );


        $stm->execute([
            $book_title,
            $author,
            $publisher,
            $publish_date,
            $category_id,
            $quantity_number,
            $available_quantity,
            $book_photo
        ]);


        // =====================================
        // SUCCESS MESSAGE
        // =====================================

        temp('info', 'Book added successfully.');


        // Go back to Book Management
        redirect('admin_panel.php?page=books');
    }
}

?>


<div class="add-book-page">

    <!-- PAGE HEADER -->

    <div class="page-header">

        <h1>Add New Book</h1>

        <p class="muted">
            Add a new book to the library collection.
        </p>

    </div>


    <!-- FORM CARD -->

    <div class="add-book-card">

        <form
            method="post"
            enctype="multipart/form-data"
        >


            <!-- =========================
                 BOOK TITLE
            ========================== -->

            <div class="form-group">

                <label for="title">
                    Book Title
                </label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    maxlength="150"
                    placeholder="Enter book title"
                    value="<?= encode($book_title) ?>"
                >

                <?php if (isset($_err['title'])): ?>

                    <span class="error">
                        <?= encode($_err['title']) ?>
                    </span>

                <?php endif; ?>

            </div>


            <!-- =========================
                 AUTHOR
            ========================== -->

            <div class="form-group">

                <label for="author">
                    Author
                </label>

                <input
                    type="text"
                    id="author"
                    name="author"
                    maxlength="100"
                    placeholder="Enter author name"
                    value="<?= encode($author) ?>"
                >

                <?php if (isset($_err['author'])): ?>

                    <span class="error">
                        <?= encode($_err['author']) ?>
                    </span>

                <?php endif; ?>

            </div>


            <!-- =========================
                 PUBLISHER
            ========================== -->

            <div class="form-group">

                <label for="publisher">
                    Publisher
                </label>

                <input
                    type="text"
                    id="publisher"
                    name="publisher"
                    maxlength="100"
                    placeholder="Enter publisher"
                    value="<?= encode($publisher) ?>"
                >

                <?php if (isset($_err['publisher'])): ?>

                    <span class="error">
                        <?= encode($_err['publisher']) ?>
                    </span>

                <?php endif; ?>

            </div>


            <!-- =========================
                 PUBLISH YEAR
            ========================== -->

            <div class="form-group">

                <label for="publish_year">
                    Publish Year
                </label>

                <input
                    type="text"
                    id="publish_year"
                    name="publish_year"
                    maxlength="4"
                    inputmode="numeric"
                    placeholder="Example: 2022"
                    value="<?= encode($publish_year) ?>"
                >

                <?php if (isset($_err['publish_year'])): ?>

                    <span class="error">
                        <?= encode($_err['publish_year']) ?>
                    </span>

                <?php endif; ?>

            </div>


            <!-- =========================
                 CATEGORY
            ========================== -->

            <div class="form-group">

                <label for="category_id">
                    Category
                </label>

                <select
                    id="category_id"
                    name="category_id"
                >

                    <option value="">
                        -- Select Category --
                    </option>


                    <?php foreach ($categories as $category): ?>

                        <option
                            value="<?= encode($category->category_id) ?>"
                            <?= (string)$category_id ===
                                (string)$category->category_id
                                ? 'selected'
                                : '' ?>
                        >

                            <?= encode($category->category_name) ?>

                        </option>

                    <?php endforeach; ?>

                </select>


                <?php if (isset($_err['category_id'])): ?>

                    <span class="error">
                        <?= encode($_err['category_id']) ?>
                    </span>

                <?php endif; ?>

            </div>


            <!-- =========================
                 QUANTITY
            ========================== -->

            <div class="form-group">

                <label for="quantity">
                    Quantity
                </label>

                <input
                    type="text"
                    id="quantity"
                    name="quantity"
                    inputmode="numeric"
                    placeholder="Example: 10"
                    value="<?= encode($quantity) ?>"
                >

                <?php if (isset($_err['quantity'])): ?>

                    <span class="error">
                        <?= encode($_err['quantity']) ?>
                    </span>

                <?php endif; ?>

            </div>


            <!-- =========================
                 BOOK PHOTO
            ========================== -->

            <div class="form-group">

                <label for="book_photo">
                    Book Photo
                </label>

                <input
                    type="file"
                    id="book_photo"
                    name="book_photo"
                    accept="image/*"
                >

                <small class="muted">
                    JPG, PNG or other image format. Maximum 2MB.
                </small>


                <?php if (isset($_err['book_photo'])): ?>

                    <span class="error">
                        <?= encode($_err['book_photo']) ?>
                    </span>

                <?php endif; ?>

            </div>


            <!-- =========================
                 BUTTONS
            ========================== -->

            <div class="form-actions">

                <button type="submit">
                    Add Book
                </button>


                <button
                    type="reset"
                    class="reset-btn"
                >
                    Reset
                </button>

            </div>


        </form>

    </div>

</div>