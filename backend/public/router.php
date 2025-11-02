<?php

/**
 * Router script for PHP built-in server
 * This allows Symfony routing to work correctly with php -S
 */

// Handle static files directly
if (is_file($_SERVER['DOCUMENT_ROOT'] . $_SERVER['SCRIPT_NAME'])) {
    return false;
}

// Route everything else through index.php
$_SERVER['SCRIPT_FILENAME'] = $_SERVER['DOCUMENT_ROOT'] . '/index.php';
$_SERVER['SCRIPT_NAME'] = '/index.php';

require $_SERVER['SCRIPT_FILENAME'];

