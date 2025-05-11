<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Hardcoded full path to your actual project folder
$basePath = '/home/u202200053/public_html/BorrowMyCharger';

// Load header
require $basePath . '/Views/template/header.phtml';

// Get lowercase action for consistency
$action = strtolower($_GET['action'] ?? 'home');

switch ($action) {
    case 'about':
        require $basePath . '/Views/about.phtml';
        break;
    case 'howitworks':
        require $basePath . '/Views/howitworks.phtml';
        break;
    case 'login':
        require $basePath . '/Views/Login.phtml';
        break;
    case 'register':
        require $basePath . '/Views/register.phtml';
        break;
    case 'home':
    default:
        require $basePath . '/Views/index.phtml';
        break;
}

// Load footer
require $basePath . '/Views/template/footer.phtml';
