<?php

use App\Controllers\UserController;

if (!isset($_SESSION['user_id'])) {
    header("Location: ?page=login");
    exit;
}

UserController::create();
