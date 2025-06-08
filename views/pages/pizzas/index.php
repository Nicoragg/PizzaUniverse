<?php

use App\Controllers\PizzaController;

$action = $_GET['action'] ?? 'list';

match ($action) {
    'create' => require __DIR__ . '/create.php',
    'edit' => require __DIR__ . '/edit.php',
    'delete' => PizzaController::delete(),
    default => PizzaController::delete()
};
