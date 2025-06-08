<?php

use App\Controllers\PizzaController;

$action = $_GET['action'] ?? 'list';

match ($action) {
    'create' => require __DIR__ . '/create.php',
    'edit' => require __DIR__ . '/edit.php',
    'menu' => require __DIR__ . '/menu.php',
    'delete' => PizzaController::delete(),
    default => PizzaController::delete()
};
