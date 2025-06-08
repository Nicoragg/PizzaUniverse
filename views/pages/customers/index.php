<?php

use App\Controllers\CustomerController;

$action = $_GET['action'] ?? 'list';

match ($action) {
    'create' => require __DIR__ . '/create.php',
    'edit' => require __DIR__ . '/edit.php',
    'delete' => CustomerController::delete(),
    'status' => CustomerController::updateStatus(),
    default => CustomerController::delete()
};
