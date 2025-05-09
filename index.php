<?php
session_start();

// Load header
require 'Views/template/header.phtml';

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

// Load footer
require 'Views/template/footer.phtml';
?>
