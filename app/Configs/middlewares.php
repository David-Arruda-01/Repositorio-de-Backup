<?php

return [
    'auth' => \App\Middlewares\AuthenticatedMiddleware::class,
    'noAuth' => \App\Middlewares\NoAuthenticatedMiddleware::class,
    'admin' => \App\Middlewares\AdminMiddleware::class,

];