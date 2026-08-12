<?php

$stm = $_db->query(
    'SELECT
        b.*,
        c.category_name
     FROM book b
     LEFT JOIN category c
        ON b.category_id = c.category_id
     ORDER BY b.book_id DESC'
);

$books = $stm->fetchAll();

?>


<div class="books-page">

    <div class="page-header">

        <h1>Book Management</h1>

        <p class="muted">
            View and manage books in the library.
        </p>

    </div>


    <div class="books-card">

        <div class="books-top">

            <h2>Book List</h2>

            <a
                href="admin_panel.php?page=add"
                class="add-book-btn"
            >
                + Add Book
            </a>

        </div>


        <?php if (empty($books)): ?>

            <p>No books available.</p>

        <?php else: ?>

            <div class="table-wrapper">

                <table class="book-table">

                    <thead>

                        <tr>
                            <th>ID</th>
                            <th>Photo</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Publisher</th>
                            <th>Year</th>
                            <th>Category</th>
                            <th>Quantity</th>
                            <th>Available</th>
                            <th>Action</th>
                        </tr>

                    </thead>


                    <tbody>

                        <?php foreach ($books as $book): ?>

                            <tr>

                                <!-- BOOK ID -->

                                <td>
                                    <?= encode($book->book_id) ?>
                                </td>


                                <!-- BOOK PHOTO -->

                                <td>

                                    <?php if (!empty($book->book_photo)): ?>

                                        <img
                                            src="../book_photo/<?= encode($book->book_photo) ?>"
                                            alt="<?= encode($book->title) ?>"
                                            class="book-cover"
                                        >

                                    <?php else: ?>

                                        <span class="no-photo">
                                            No Photo
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- TITLE -->

                                <td>
                                    <?= encode($book->title) ?>
                                </td>


                                <!-- AUTHOR -->

                                <td>
                                    <?= encode($book->author) ?>
                                </td>


                                <!-- PUBLISHER -->

                                <td>
                                    <?= encode($book->publisher) ?>
                                </td>


                                <!-- PUBLISH YEAR -->

                                <td>

                                    <?php if (
                                        !empty($book->publish_year) &&
                                        $book->publish_year !== '0000-00-00'
                                    ): ?>

                                        <?= encode(
                                            date(
                                                'Y',
                                                strtotime($book->publish_year)
                                            )
                                        ) ?>

                                    <?php else: ?>

                                        -

                                    <?php endif; ?>

                                </td>


                                <!-- CATEGORY -->

                                <td>
                                    <?= encode(
                                        $book->category_name ?? '-'
                                    ) ?>
                                </td>


                                <!-- QUANTITY -->

                                <td>
                                    <?= encode($book->quantity) ?>
                                </td>


                                <!-- AVAILABLE QUANTITY -->

                                <td>
                                    <?= encode(
                                        $book->available_quantity
                                    ) ?>
                                </td>


                                <!-- ACTION -->

                                <td class="action-cell">

                                    <a
                                        href="admin_panel.php?page=modify&id=<?= $book->book_id ?>"
                                        class="edit-btn"
                                    >
                                        Edit
                                    </a>


                                    <a
                                        href="delete.php?id=<?= $book->book_id ?>"
                                        class="delete-btn"
                                        onclick="return confirm('Are you sure you want to delete this book?')"
                                    >
                                        Delete
                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </div>

</div>