<?php

use App\Controllers\PizzaController;

if (!isset($_SESSION['user_id'])) {
    header("Location: ?page=login");
    exit;
}

$action = $_GET['action'] ?? 'list';

match ($action) {
    'create' => require __DIR__ . '/create.php',
    'edit' => require __DIR__ . '/edit.php',
    'menu' => require __DIR__ . '/menu.php',
    'delete' => PizzaController::delete(),
    default => PizzaController::delete()
};
