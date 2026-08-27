<?php

require_once '../_base.php';

$title = 'My Wishlist';
$_title = '';


// =========================================
// LOGIN CHECK
// 一定要在 customer_header.php 前面
// =========================================

if (!$_user) {
    redirect('/login.php');
}

$user_id = $_user->user_id;


// =========================================
// HANDLE REMOVE FROM WISHLIST
// 一定要先处理 POST，再输出 HTML
// =========================================

if (
    is_post() &&
    post('action') === 'remove'
) {

    $wishlist_id = (int)post('wishlist_id');

    if ($wishlist_id > 0) {

        $stm = $_db->prepare("
            DELETE FROM wishlist
            WHERE wishlist_id = ?
            AND user_id = ?
        ");

        $stm->execute([
            $wishlist_id,
            $user_id
        ]);

        temp(
            'info',
            'Book removed from wishlist successfully!'
        );
    }

    redirect('/customer/wishlist.php');
}


// =========================================
// HANDLE ADD TO CART
// 如果以后还需要这个功能可以保留
// =========================================

if (
    is_post() &&
    post('action') === 'add_to_cart'
) {

    $book_id = (int)post('book_id');


    $stm = $_db->prepare("
        SELECT available_quantity
        FROM book
        WHERE book_id = ?
    ");

    $stm->execute([
        $book_id
    ]);

    $book = $stm->fetch();


    if (
        $book &&
        $book->available_quantity > 0
    ) {

        temp(
            'info',
            'Book added to cart!'
        );

    } else {

        temp(
            'info',
            'Book is currently unavailable'
        );

    }


    redirect('/customer/wishlist.php');
}


// =========================================
// GET WISHLIST ITEMS
// POST 处理完成之后才读取
// =========================================

$stm = $_db->prepare("
    SELECT
        w.wishlist_id,
        w.book_id,
        w.created_at,

        bk.title,
        bk.author,
        bk.book_photo,
        bk.available_quantity

    FROM wishlist w

    JOIN book bk
        ON w.book_id = bk.book_id

    WHERE w.user_id = ?

    ORDER BY w.created_at DESC
");

$stm->execute([
    $user_id
]);

$wishlist = $stm->fetchAll();


// =========================================
// 最后才加载 HEADER
// =========================================

include 'customer_header.php';

?>


<div class="wishlist-page">


    <!-- =====================================
         PAGE HEADER
    ====================================== -->

    <div class="page-header">

        <h1>
            ❤️ My Wishlist
        </h1>

        <p>
            Books you've saved for later
        </p>

    </div>



    <?php if (empty($wishlist)): ?>


        <!-- =================================
             EMPTY STATE
        ================================== -->

        <div
            class="empty-state"
            style="text-align:center;"
        >

            <span class="empty-icon">
                📚
            </span>


            <h2 style="text-align:center;">
                Your wishlist is empty
            </h2>


            <p style="text-align:center;">
                Start adding books you'd like to read!
            </p>


            <a
                href="/customer/catalog.php"
                class="btn-browse"
            >
                Browse Books →
            </a>

        </div>



    <?php else: ?>


        <!-- =================================
             WISHLIST GRID
        ================================== -->

        <div class="wishlist-grid">


            <?php foreach ($wishlist as $item): ?>


                <div class="wishlist-card">


                    <!-- =========================
                         BOOK IMAGE
                    ========================== -->

                    <img
                        src="/admin/book_photo/<?= encode(
                            $item->book_photo ?: 'default.png'
                        ) ?>"
                        alt="<?= encode($item->title) ?>"
                    >



                    <!-- =========================
                         BOOK INFO
                    ========================== -->

                    <div class="wishlist-info">


                        <h3 class="wishlist-name">

                            <?= encode($item->title) ?>

                        </h3>


                        <p class="wishlist-author">

                            by <?= encode($item->author) ?>

                        </p>


                        <p
                            class="
                                wishlist-stock
                                <?= $item->available_quantity > 0
                                    ? 'in-stock'
                                    : 'out-of-stock'
                                ?>
                            "
                        >

                            <?=
                                $item->available_quantity > 0
                                    ? '✅ In Stock'
                                    : '❌ Out of Stock'
                            ?>

                        </p>


                    </div>



                    <!-- =========================
                         ACTION BUTTONS
                    ========================== -->

                    <div class="wishlist-actions">


                        <!-- VIEW DETAILS -->

                        <a
                            href="/customer/book_details.php?id=<?= $item->book_id ?>"
                            class="btn-view"
                        >

                            📖 View Details

                        </a>



                        <!-- BORROW -->

                        <?php if (
                            $item->available_quantity > 0
                        ): ?>


                            <form
                                method="post"
                                action="/customer/borrow.php"
                                style="width:100%;"
                            >


                                <input
                                    type="hidden"
                                    name="book_id"
                                    value="<?= $item->book_id ?>"
                                >


                                <button
                                    type="submit"
                                    class="btn-borrow"
                                >

                                    ✓ Borrow Now

                                </button>


                            </form>


                        <?php else: ?>


                            <button
                                type="button"
                                class="btn-borrow"
                                disabled
                            >

                                Out of Stock

                            </button>


                        <?php endif; ?>



                        <!-- REMOVE -->

                        <form
                            method="post"
                            style="width:100%;"
                        >


                            <input
                                type="hidden"
                                name="action"
                                value="remove"
                            >


                            <input
                                type="hidden"
                                name="wishlist_id"
                                value="<?= $item->wishlist_id ?>"
                            >


                            <button
                                type="submit"
                                class="btn-remove"
                                onclick="
                                    return confirm(
                                        'Remove this book from wishlist?'
                                    )
                                "
                            >

                                🗑️ Remove

                            </button>


                        </form>


                    </div>

                </div>


            <?php endforeach; ?>


        </div>


    <?php endif; ?>


</div>


</main>


<?php include '../footer.php'; ?>