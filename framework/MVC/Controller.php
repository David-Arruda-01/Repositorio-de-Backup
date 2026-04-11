<?php

namespace Fmk\MVC;

use Fmk\Traits\Middleware;
use Fmk\Utils\Request;

class Controller
{
    use Middleware;

    public function request()
    {
        return Request::getInstance(); // Use um método estático adequado
    }

    function isAdmin()
    {
        return ($_SESSION['user']['tipo'] ?? null) === 'admin';
    }
}
