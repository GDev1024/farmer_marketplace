<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    redirect('../index.php');
}

jsonResponse(['success' => false, 'message' => 'Invalid action'], 400);