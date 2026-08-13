<?php
$navCategories = $_db
    ->query('SELECT * FROM category ORDER BY category_name')
    ->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title><?= $_title ?? 'Book Nest' ?></title>

    <link
        rel="shortcut icon"
        href="/images/favicon.png"
    >

    <link
        rel="stylesheet"
        href="/css/app.css"
    >

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script src="/js/app.js"></script>
</head>
<body>

    <?php if ($info = temp('info')): ?>
        <div id="info">
            <?= $info ?>
        </div>
    <?php endif; ?>


    <header>

        <div class="auth">

            <?php if (!isset($_SESSION['user'])): ?>

                <a href="/login.php">
                    Login
                </a>

                <span>|</span>

                <a href="/register.php">
                    Register
                </a>

            <?php else: ?>

                <a
                    href="/logout.php"
                    onclick="return confirm('Do you want to logout?')"
                >
                    Logout
                </a>


                <div class="user-photo-dropdown">

                    <img
                        src="/photo/<?= encode(
                            $_SESSION['user']->profile_photo ?? ''
                        ) ?>"
                        alt="Profile Photo"
                    >

                    <div class="dropdown-content">
                        <a href="/customer/customer_profile.php">My Profile</a>
                        <a href="/customer/borrowing_history.php">My Borrowing History</a>
                        <a href="/customer/change_password.php">Change Password</a>
                        <a href="/customer/wishlist.php">My WishList</a>
                        <a href="/customer/fines.php">My Fine</a>
                    </div>

                </div>

            <?php endif; ?>

        </div>

    </header>


    <nav class="navbar">

        <a href="/">
            Book Nest
        </a>


        <div class="menu">

            <a href="/home.php">
                <b>Home</b>
            </a>

            <a href="/customer/category.php">
                <b>Categories</b>
            </a>

            <a href="/customer/new_arrivals.php">
                <b>New Arrivals</b>
            </a>

            <a href="/customer/about_us.php">
                <b>About Us</b>
            </a>

        </div>


        <div class="right">

            <div class="search-bar">

                <form
                    action="/customer/search.php"
                    method="get"
                    class="search-form"
                >

                    <input
                        type="search"
                        id="search"
                        name="product_name"
                        placeholder="Search books"
                    >

                    <button type="submit">
                        Search
                    </button>

                </form>

            </div>

        </div>

    </nav>


    <main>

        <h1 class="page-title">
            <?= $_title ?? 'Untitled' ?>
        </h1>

            <script>
    document.addEventListener('DOMContentLoaded', function() {
        var dropdown = document.querySelector('.user-photo-dropdown');
        if (dropdown) {
            var img = dropdown.querySelector('img');
            if (img) {
                img.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dropdown.classList.toggle('active');
                });
            }
        }
        document.addEventListener('click', function(e) {
            if (dropdown && !dropdown.contains(e.target)) {
                dropdown.classList.remove('active');
            }
        });
    });
    </script>