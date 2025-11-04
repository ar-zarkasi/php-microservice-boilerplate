<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
use Hyperf\HttpServer\Router\Router;
use App\Controller\{IndexController, LoginController, UsersController};
use App\Middleware\AuthMiddleware;

Router::addRoute(['GET', 'POST', 'HEAD'], '/', [IndexController::class, 'index']);

Router::get('/favicon.ico', function () {
    return '';
});

Router::addGroup('/v1', function (){
    Router::post('/login', [LoginController::class, 'index']);
    Router::get('/profile', [LoginController::class, 'profile'],['middleware' => [AuthMiddleware::class]]);
    Router::post('/logout', [LoginController::class, 'logout'], ['middleware' => [AuthMiddleware::class]]);

    // Add more routes here
   Router::addGroup('/users', function (){
        Router::get('', [UsersController::class, 'index']);
        Router::addRoute(['GET'], '/{id:[0-9a-f\-]+}', [UsersController::class, 'show']);
        Router::addRoute(['POST'], '/{id:[0-9a-f\-]+}', [UsersController::class, 'update']);
   }, ['middleware' => [AuthMiddleware::class]]);
});
