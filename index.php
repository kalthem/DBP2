<?php
session_start();
<<<<<<< Updated upstream

// Load header
require 'Views/template/header.phtml';
=======
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Hardcoded full path to your actual project folder
$basePath = '/home/u202103011/public_html/BorrowMyCharger';

>>>>>>> Stashed changes

// Get lowercase action for consistency
$action = strtolower($_GET['action'] ?? 'home');

switch ($action) {
    case 'about':
        require 'Views/about.phtml';
        break;
    case 'howitworks':
        require 'Views/howitworks.phtml';
        break;
    case 'home':
    default:
        require 'Views/index.phtml';
        break;
}

<<<<<<< Updated upstream
// Load footer
require 'Views/template/footer.phtml';
?>
=======
>>>>>>> Stashed changes
