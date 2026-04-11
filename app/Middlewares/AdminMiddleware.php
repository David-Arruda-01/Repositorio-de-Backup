<?php

namespace App\Middlewares;

use Fmk\Interfaces\Middleware;
use Fmk\Utils\Router;

class AdminMiddleware implements Middleware
{
    public function check(): bool
    {
        return isAdmin();
    }

    public function handle()
    {
        return Router::error403("Acesso negado. Apenas administradores podem acessar esta página.");
    }
}
