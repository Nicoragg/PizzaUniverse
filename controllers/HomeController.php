<?php

namespace App\Controllers;

abstract class HomeController
{
    public static function index(): void
    {
        require './views/pages/home.php';
    }
}
