<?php

require_once __DIR__ . '/../_base.php';

if (!isset($_SESSION['user']) || $_SESSION['user']->role !== 'admin') {
    redirect('../login.php');
}

// Get current page
$page = $_GET['page'] ?? 'profile';

$fetch_profile = $_SESSION['user'];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title><?= encode($title ?? 'Admin Panel') ?></title>

    <link
        rel="shortcut icon"
        href="../images/favicon.png"
    >

  <link rel="stylesheet" href="../css/app.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script src="../js/app.js"></script>

</head>


<body class="admin-layout">

    <div class="sidebar">

        <h2>
            <i>Admin Panel</i>
        </h2>


        <div class="right">

            <!-- PROFILE -->

            <a
                href="admin_panel.php?page=profile"
                class="<?= $page === 'profile' ? 'active' : '' ?>"
            >
                Profile 👤
            </a>


            <!-- BOOKS -->

            <a
                href="admin_panel.php?page=books"
                class="<?= $page === 'books' ? 'active' : '' ?>"
            >
                Books 📚
            </a>


            <!-- ADD BOOK -->

            <a
                href="admin_panel.php?page=add"
                class="<?= $page === 'add' ? 'active' : '' ?>"
            >
                Add Book ➕
            </a>


            <!-- USERS -->

            <a
                href="admin_panel.php?page=users"
                class="<?= $page === 'users' ? 'active' : '' ?>"
            >
                Users 👥
            </a>


        </div>


        <div class="logout-btn">

            <a href="../logout.php">
                Logout
            </a>

        </div>


    </div>


    <div class="main-content">