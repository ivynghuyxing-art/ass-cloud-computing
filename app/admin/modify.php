<?php

require_once __DIR__ . '/../_base.php';

$_err = [];

$title = 'Edit Book';


// =====================================
// GET BOOK ID
// =====================================

$book_id = $_GET['id'] ?? '';

if ($book_id === '' || !ctype_digit((string)$book_id)) {
    redirect('admin_panel.php?page=books');
}


// =====================================
// GET BOOK
// =====================================

$stm = $_db->prepare(
    'SELECT *
     FROM book
     WHERE book_id = ?'
);

$stm->execute([$book_id]);

$book = $stm->fetch();

if (!$book) {
    redirect('admin_panel.php?page=books');
}


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

$book_title = $book->title;
$author = $book->author;
$publisher = $book->publisher;

$publish_year =
    !empty($book->publish_year) &&
    $book->publish_year !== '0000-00-00'
        ? date('Y', strtotime($book->publish_year))
        : '';

$category_id = $book->category_id;
$quantity = $book->quantity;

$old_quantity = (int)$book->quantity;
$old_available = (int)$book->available_quantity;

$book_photo = $book->book_photo ?? '';


// =====================================
// UPDATE BOOK
// =====================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $book_title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $publisher = trim($_POST['publisher'] ?? '');
    $publish_year = trim($_POST['publish_year'] ?? '');
    $category_id = $_POST['category_id'] ?? '';
    $quantity = trim($_POST['quantity'] ?? '');

    $f = get_file('book_photo');


    // =====================================
    // VALIDATION
    // =====================================

    if ($book_title === '') {

        $_err['title'] = 'Book title is required.';

    } elseif (strlen($book_title) > 150) {

        $_err['title'] = 'Maximum 150 characters.';
    }


    if ($author === '') {

        $_err['author'] = 'Author is required.';

    } elseif (strlen($author) > 100) {

        $_err['author'] = 'Maximum 100 characters.';
    }


    if ($publisher === '') {

        $_err['publisher'] = 'Publisher is required.';

    } elseif (strlen($publisher) > 100) {

        $_err['publisher'] = 'Maximum 100 characters.';
    }


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


    if ($category_id === '') {

        $_err['category_id'] = 'Please select a category.';
    }


    if ($quantity === '') {

        $_err['quantity'] = 'Quantity is required.';

    } elseif (!ctype_digit($quantity)) {

        $_err['quantity'] = 'Quantity must be a number.';

    } elseif ((int)$quantity < 1) {

        $_err['quantity'] = 'Quantity must be at least 1.';
    }


    // New photo is optional in Edit
    if ($f) {

        if (!str_starts_with($f->type, 'image/')) {

            $_err['book_photo'] = 'File must be an image.';

        } elseif ($f->size > 2 * 1024 * 1024) {

            $_err['book_photo'] = 'Maximum photo size is 2MB.';
        }
    }


    // =====================================
    // UPDATE DATABASE
    // =====================================

    if (empty($_err)) {

        $quantity_number = (int)$quantity;

        $publish_date = $publish_year . '-01-01';


        // Keep borrowed quantity correct
        $borrowed_quantity =
            $old_quantity - $old_available;

        $available_quantity =
            $quantity_number - $borrowed_quantity;

        if ($available_quantity < 0) {
            $available_quantity = 0;
        }


        // =====================================
        // UPDATE PHOTO
        // =====================================

        if ($f) {

            $old_photo = $book_photo;

            $new_photo = save_photo(
                $f,
                __DIR__ . '/book_photo'
            );

            if ($new_photo) {

                $book_photo = $new_photo;

                if (
                    $old_photo !== '' &&
                    file_exists(
                        __DIR__ .
                        '/book_photo/' .
                        $old_photo
                    )
                ) {

                    unlink(
                        __DIR__ .
                        '/book_photo/' .
                        $old_photo
                    );
                }
            }
        }


        // =====================================
        // UPDATE BOOK
        // =====================================

        $stm = $_db->prepare(
            'UPDATE book
             SET
                title = ?,
                author = ?,
                publisher = ?,
                publish_year = ?,
                category_id = ?,
                quantity = ?,
                available_quantity = ?,
                book_photo = ?
             WHERE book_id = ?'
        );


        $stm->execute([
            $book_title,
            $author,
            $publisher,
            $publish_date,
            $category_id,
            $quantity_number,
            $available_quantity,
            $book_photo,
            $book_id
        ]);


        temp(
            'info',
            'Book updated successfully.'
        );

        redirect(
            'admin_panel.php?page=books'
        );
    }
}

?>


<div class="add-book-page">

    <div class="page-header">

        <h1>Edit Book</h1>

        <p class="muted">
            Update book information.
        </p>

    </div>


    <div class="add-book-card">

        <form
            method="post"
            enctype="multipart/form-data"
        >


            <!-- BOOK TITLE -->

            <div class="form-group">

                <label for="title">
                    Book Title
                </label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    maxlength="150"
                    value="<?= encode($book_title) ?>"
                >

                <?php if (isset($_err['title'])): ?>

                    <span class="error">
                        <?= encode($_err['title']) ?>
                    </span>

                <?php endif; ?>

            </div>


            <!-- AUTHOR -->

            <div class="form-group">

                <label for="author">
                    Author
                </label>

                <input
                    type="text"
                    id="author"
                    name="author"
                    maxlength="100"
                    value="<?= encode($author) ?>"
                >

                <?php if (isset($_err['author'])): ?>

                    <span class="error">
                        <?= encode($_err['author']) ?>
                    </span>

                <?php endif; ?>

            </div>


            <!-- PUBLISHER -->

            <div class="form-group">

                <label for="publisher">
                    Publisher
                </label>

                <input
                    type="text"
                    id="publisher"
                    name="publisher"
                    maxlength="100"
                    value="<?= encode($publisher) ?>"
                >

                <?php if (isset($_err['publisher'])): ?>

                    <span class="error">
                        <?= encode($_err['publisher']) ?>
                    </span>

                <?php endif; ?>

            </div>


            <!-- PUBLISH YEAR -->

            <div class="form-group">

                <label for="publish_year">
                    Publish Year
                </label>

                <input
                    type="text"
                    id="publish_year"
                    name="publish_year"
                    maxlength="4"
                    value="<?= encode($publish_year) ?>"
                >

                <?php if (isset($_err['publish_year'])): ?>

                    <span class="error">
                        <?= encode($_err['publish_year']) ?>
                    </span>

                <?php endif; ?>

            </div>


            <!-- CATEGORY -->

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


            <!-- QUANTITY -->

            <div class="form-group">

                <label for="quantity">
                    Quantity
                </label>

                <input
                    type="text"
                    id="quantity"
                    name="quantity"
                    value="<?= encode($quantity) ?>"
                >

                <?php if (isset($_err['quantity'])): ?>

                    <span class="error">
                        <?= encode($_err['quantity']) ?>
                    </span>

                <?php endif; ?>

            </div>


            <!-- CURRENT PHOTO -->

            <div class="form-group">

                <label>
                    Current Book Photo
                </label>

                <?php if ($book_photo !== ''): ?>

                    <img
                        src="/admin/book_photo/<?= encode($book_photo) ?>"
                        alt="<?= encode($book_title) ?>"
                        style="
                            width:120px;
                            height:160px;
                            object-fit:cover;
                            border-radius:8px;
                            margin-bottom:10px;
                        "
                    >

                <?php else: ?>

                    <p>No Photo</p>

                <?php endif; ?>

            </div>


            <!-- NEW PHOTO -->

            <div class="form-group">

                <label for="book_photo">
                    Change Book Photo
                </label>

                <input
                    type="file"
                    id="book_photo"
                    name="book_photo"
                    accept="image/*"
                >

                <?php if (isset($_err['book_photo'])): ?>

                    <span class="error">
                        <?= encode($_err['book_photo']) ?>
                    </span>

                <?php endif; ?>

            </div>


            <!-- BUTTONS -->

            <div class="form-actions">

                <button type="submit">
                    Save Changes
                </button>

                <a
                    href="admin_panel.php?page=books"
                    class="reset-btn"
                    style="
                        text-decoration:none;
                        display:inline-block;
                    "
                >
                    Cancel
                </a>

            </div>

        </form>

    </div>

</div>