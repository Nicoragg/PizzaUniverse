<?php

namespace App;

require_once './autoload.php';

$page = $_GET['page'] ?? 'login';

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pizza Universe - Admin</title>
  <link rel="stylesheet" href="./assets/stylesheets/style.css">
  <?php
  $crudPages = ['users', 'pizzas', 'customers'];

  if (in_array($page, $crudPages)) {
    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
    echo '<link rel="stylesheet" href="./assets/stylesheets/crud.css">';
  }

  $pageStyles = [
    'login' => 'auth.css',
    'home' => 'home.css',
    'dashboard' => 'dashboard.css',
    'pizzas' => 'pizzas.css',
    'customers' => 'customers.css',
    'orders' => 'orders.css',
  ];

  if (isset($pageStyles[$page])) {
    echo '<link rel="stylesheet" href="./assets/stylesheets/' . $pageStyles[$page] . '">';
  }
  ?>
  <link rel="shortcut icon" href="./assets/images/logo.png" type="image/x-icon">

  <?php
  $pageScripts = [
    'pizzas' => ['sweetalert-confirm.js', 'pizzas.js'],
    'users' => ['sweetalert-confirm.js', 'users.js'],
    'customers' => ['sweetalert-confirm.js', 'customers.js'],
    'orders' => ['orders.js']
  ];

  if (isset($pageScripts[$page])) {
    foreach ($pageScripts[$page] as $script) {
      echo '<script src="./assets/js/' . $script . '" defer></script>';
    }
  }
  ?>
</head>

<body>
  <?php

  if (!in_array($page, ['login']) && session_status() === PHP_SESSION_NONE) {
    session_start();
  }

  if (!in_array($page, ['login']) && !isset($_SESSION['user_id'])) {
    header("Location: ?page=login");
    exit;
  }

  if ($page !== 'login') {
    require_once("./views/components/header.php");
  }

  match ($page) {
    'login'     => \App\Controllers\UserController::login(),
    'logout'    => \App\Controllers\UserController::logout(),
    'dashboard' => \App\Controllers\DashboardController::index(),
    'users'     => \App\Controllers\UserController::handleRoutes(),
    'pizzas'    => \App\Controllers\PizzaController::handleRoutes(),
    'customers' => \App\Controllers\CustomerController::handleRoutes(),
    'orders'    => \App\Controllers\OrderController::handleRoutes(),
    'home'      => \App\Controllers\HomeController::index(),
    default     => \App\Controllers\PageController::notFound(),
  };

  if ($page !== 'login') {
    require_once("./views/components/footer.php");
  }
  ?>
</body>

</html>