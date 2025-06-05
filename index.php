<?php 

namespace App;

require_once "./autoload.php";
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pizza Universe</title>
  <link rel="stylesheet" href="./assets/stylesheets/style.css">
  <link rel="stylesheet" href="./assets/stylesheets/auth.css">
  <link rel="shortcut icon" href="./assets/images/logo.png" type="image/x-icon">
</head>


<body>
  <?php
  require_once("./views/components/header.php");

  $page = $_GET['page'] ?? 'menu';

  match ($page) {
    'home'    => require './views/pages/home.php',
    'menu'    => require './views/pages/menu.php',
    'about'   => require './views/pages/about.php',
    'contact' => require './views/pages/contact.php',
    'deliver' => require './views/pages/deliver.php',
    'register' => require './views/pages/register.php',
    'login' => require './views/pages/login.php',
    default   => require './views/pages/404.php',
  };

  require_once("./views/components/footer.php");
  ?>
</body>

</html>
