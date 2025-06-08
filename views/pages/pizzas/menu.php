<?php

use App\Controllers\PizzaController;

if (!isset($_SESSION['user_id'])) {
    header("Location: ?page=login");
    exit;
}

PizzaController::menu();
