<?php
use App\Middleware\AuthMiddleware;

return function ($app) {
    $app->group('', function ($group) {
        $group->post('/orders', 'orderController:create');
        $group->get('/orders/active', 'orderController:listActive');
        $group->post('/orders/{id}/complete', 'orderController:complete');
    })->add(new AuthMiddleware());
};