<?php

use App\Controllers\OrderController;

$action = $_GET['action'] ?? 'list';

match ($action) {
    'create' => require __DIR__ . '/create.php',
    'view' => require __DIR__ . '/view.php',
    'update_status' => OrderController::updateStatus(),
    'delete' => OrderController::delete(),
    default => OrderController::findAll()
};
