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

  if ($page === 'menu') {
    $_GET['page'] = 'pizzas';
    $_GET['action'] = 'menu';
    $page = 'pizzas';
  }

  $pageStyles = [
    'login' => 'auth.css',
    'home' => 'home.css',
    'dashboard' => 'dashboard.css',
    'pizzas' => 'pizzas.css',
    'customers' => 'customers.css',
    'deliver' => 'deliver.css'
  ];

  if (isset($pageStyles[$page])) {
    if (is_array($pageStyles[$page])) {
      foreach ($pageStyles[$page] as $styleFile) {
        echo '<link rel="stylesheet" href="./assets/stylesheets/' . $styleFile . '">';
      }
    } else {
      echo '<link rel="stylesheet" href="./assets/stylesheets/' . $pageStyles[$page] . '">';
    }
  }
  ?>
  <link rel="shortcut icon" href="./assets/images/logo.png" type="image/x-icon">

  <?php
  $pageScripts = [
    'pizzas' => ['sweetalert-confirm.js', 'pizzas.js'],
    'users' => ['sweetalert-confirm.js', 'users.js'],
    'customers' => ['sweetalert-confirm.js', 'customers.js']
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
    'login'     => require './views/pages/login.php',
    'dashboard' => require './views/pages/dashboard.php',
    'users'     => require './views/pages/users/index.php',
    'pizzas'    => require './views/pages/pizzas/index.php',
    'customers' => require './views/pages/customers/index.php',
    'home'      => require './views/pages/home.php',
    'deliver'   => require './views/pages/deliver.php',
    default     => require './views/pages/404.php',
  };

  if ($page !== 'login') {
    require_once("./views/components/footer.php");
  }
  ?>
</body>

</html>