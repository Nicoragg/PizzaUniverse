<?php

namespace App\Controllers;

abstract class PageController
{
    public static function notFound(): void
    {
        http_response_code(404);
        require './views/pages/404.php';
    }
}
