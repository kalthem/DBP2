<?php
session_start();

// Load shared layout + main content view
require 'views/template/header.phtml';
require 'views/index.phtml';
require 'views/template/footer.phtml';
?>
