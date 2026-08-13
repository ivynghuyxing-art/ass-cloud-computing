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


    case 'books':
        require __DIR__ . '/books.php';
        break;


    case 'add':
        require __DIR__ . '/add.php';
        break;

    case 'category':
        require __DIR__ . '/category.php';
        break;  

    case 'modify':
        require __DIR__ . '/modify.php';
        break;

    case 'users':
        require __DIR__ . '/users.php';
        break;
        
    case 'edit_user':
    require __DIR__ . '/edit_user.php';
    break;

    default:
        require __DIR__ . '/profile.php';
        break;

    
}


require __DIR__ . '/admin_footer.php';