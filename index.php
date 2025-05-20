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
  require_once("./view/components/header.php");

  $page = $_GET['page'] ?? 'menu';

  match ($page) {
    'home'    => require './view/pages/home.php',
    'menu'    => require './view/pages/menu.php',
    'about'   => require './view/pages/about.php',
    'contact' => require './view/pages/contact.php',
    'deliver' => require './view/pages/deliver.php',
    'register' => require './view/pages/register.php',
    'login' => require './view/pages/login.php',
    default   => require './view/pages/404.php',
  };

  require_once("./view/components/footer.php");
  ?>
</body>

</html>
