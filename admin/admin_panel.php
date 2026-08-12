<?php

require_once __DIR__ . '/../_base.php';

if (!isset($_SESSION['user']) || $_SESSION['user']->role !== 'admin') {
    redirect('../login.php');
}

$page = $_GET['page'] ?? 'profile';

$title = 'Admin Panel';

require __DIR__ . '/admin_header.php';


switch ($page) {

    case 'profile':

        require __DIR__ . '/profile.php';

        break;


    case 'add':

        require __DIR__ . '/add.php';

        break;


    case 'modify':

        require __DIR__ . '/modify.php';

        break;


    default:

        require __DIR__ . '/profile.php';

        break;
}


require __DIR__ . '/../footer.php';