<?php

use App\Controllers\UserController;

$action = $_GET['action'] ?? 'list';

match ($action) {
    'create' => require __DIR__ . '/create.php',
    'edit' => require __DIR__ . '/edit.php',
    'delete' => UserController::delete(),
    default => UserController::delete()
};
