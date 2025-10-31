<?php
namespace App\Controller;

use App\Service\OrderServiceInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Validator\OrderValidator;
use App\Http\Exceptions\HttpException;

class OrderController
{
    private $service;

    public function __construct(OrderServiceInterface $service)
    {
        $this->service = $service;
    }

    public function create(Request $req, Response $res)
    {
        try {
            $data = $req->getParsedBody();
            OrderValidator::validateCreate($data);

            $dt = new \DateTime($data['pickup_time']);
            $data['pickup_time'] = $dt->format('Y-m-d H:i:s');
            $result = $this->service->createOrder($data);
            return $res->withJson(['data' => $result], 201);

        } catch (HttpException $e) {
            throw new HttpException($e->getMessage(), $e->getCode());
        }
    }

    public function listActive(Request $req, Response $res)
    {
        try {
            $orders = $this->service->listActive();
            return $res->withJson(['data' => $orders]);
        } catch (HttpException $e) {
            throw new HttpException($e->getMessage(), $e->getCode());
        }
    }

    public function complete(Request $req, Response $res, array $args)
    {
        try {
            $this->service->completeOrder((int)$args['id']);
            return $res->withJson(['status' => 'completed']);
        } catch (HttpException $e) {
            throw new HttpException($e->getMessage(), $e->getCode());
        }
    }
}
