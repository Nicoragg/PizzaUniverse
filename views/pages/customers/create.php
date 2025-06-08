<?php

use App\Controllers\CustomerController;

if (!isset($_SESSION['user_id'])) {
    header("Location: ?page=login");
    exit;
}

CustomerController::create();
