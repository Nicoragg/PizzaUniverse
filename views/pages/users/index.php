<?php

use App\Controllers\UserController;

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ?page=login");
    exit;
}

$action = $_GET['action'] ?? 'list';

match ($action) {
    'create' => require __DIR__ . '/create.php',
    'edit' => require __DIR__ . '/edit.php',
    'delete' => UserController::delete(),
    default => UserController::findAll($_GET['del'] ?? null)
};
