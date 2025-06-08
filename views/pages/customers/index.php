<?php

use App\Controllers\CustomerController;

if (!isset($_SESSION['user_id'])) {
    header("Location: ?page=login");
    exit;
}

$action = $_GET['action'] ?? 'list';

match ($action) {
    'create' => require __DIR__ . '/create.php',
    'edit' => require __DIR__ . '/edit.php',
    'delete' => CustomerController::delete(),
    default => CustomerController::delete()
};
