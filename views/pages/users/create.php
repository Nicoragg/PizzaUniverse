<?php

use App\Controllers\UserController;

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ?page=login");
    exit;
}

UserController::create();
