<?php

namespace App;

require_once "./autoload.php";
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pizza Universe - Admin</title>
  <link rel="stylesheet" href="./assets/stylesheets/style.css">
  <link rel="stylesheet" href="./assets/stylesheets/auth.css">
  <link rel="shortcut icon" href="./assets/images/logo.png" type="image/x-icon">
</head>

<body>
  <?php
  $page = $_GET['page'] ?? 'login';

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
    'home'      => require './views/pages/home.php',
    'menu'      => require './views/pages/menu.php',
    'deliver'   => require './views/pages/deliver.php',
    default     => require './views/pages/404.php',
  };

  // Exibe footer apenas se não for login
  if ($page !== 'login') {
    require_once("./views/components/footer.php");
  }
  ?>
</body>

</html>