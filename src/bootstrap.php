<?php
error_reporting(E_ALL);
use Slim\App;
use App\Db\PDOSingleton;
use App\Repository\OrderRepository;
use App\Service\OrderService;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Http\Exceptions\HttpException;


$settings = require __DIR__ . '/Settings.php';
$app = new App($settings);

// init singleton PDO
PDOSingleton::init($settings['settings']);

// container
$container = $app->getContainer();

// logger
$container['logger'] = function($c) use ($settings) {
    $logger = new Logger('app');
    $logpath = $settings['settings']['logger']['path'];
    $logger->pushHandler(new StreamHandler($logpath, Logger::DEBUG));
    return $logger;
};

// PDO
$container['pdo'] = function($c) {
    return \App\Db\PDOSingleton::getInstance()->getConnection();
};

// repository binding (Dependency Inversion)
$container[\App\Repository\OrderRepositoryInterface::class] = function($c) {
    return new OrderRepository($c->get('pdo'));
};

// service
$container[\App\Service\OrderServiceInterface::class] = function($c) {
    return new OrderService($c->get(\App\Repository\OrderRepositoryInterface::class), $c->get('settings')['kitchen_capacity']);
};

$container['orderController'] = function ($c) {
    return new \App\Controller\OrderController(
        $c->get(\App\Service\OrderServiceInterface::class)
    );
};

$container['errorHandler'] = function ($c) {
    return function (Request $request, Response $response, Throwable $exception) use ($c) {
        // Handle custom HttpException
        if ($exception instanceof HttpException) {
            $payload = [
                'error' => $exception->getMessage(),
            ];
            return $response
                ->withStatus($exception->getCode() ?: 400)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode($payload));
        }

        // Handle any other exceptions
        // $c->get('logger')->error($exception->getMessage());
        // $payload = ['error' => 'Internal Server Error'];
        // return $response
        //     ->withStatus(500)
        //     ->withHeader('Content-Type', 'application/json')
        //     ->write(json_encode($payload));
    };
};

return $app;