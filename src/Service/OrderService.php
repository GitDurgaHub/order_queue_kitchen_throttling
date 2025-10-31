<?php
namespace App\Service;

use App\Repository\OrderRepositoryInterface;
use App\Model\OrderModel;
use App\Http\Exceptions\HttpException;

class OrderService implements OrderServiceInterface
{
    private $repo;
    private $capacity;

    public function __construct(OrderRepositoryInterface $repo, int $capacity = 5)
    {
        $this->repo = $repo;
        $this->capacity = $capacity;
    }

    public function createOrder(array $data): array
    {
        $order = new OrderModel($data);

        if (!$order->vip) {
            $active = $this->repo->countActive();
            if ($active >= $this->capacity) {
                throw new HttpException('Kitchen full', 429);
            }
        }

        $id = $this->repo->create($order);
        return ['id' => $id, 'status' => 'created'];
    }

    public function listActive(): array
    {
        return array_map(function (OrderModel $m) {
            return [
                'id' => $m->id,
                'items' => $m->items,
                'pickup_time' => $m->pickup_time,
                'vip' => $m->vip,
                'status' => $m->status
            ];
        }, $this->repo->findActive());
    }

    public function completeOrder(int $id): bool
    {
        $order = $this->repo->findById($id);
        if (!$order) throw new HttpException('No active order found ', 404);
        return $this->repo->markComplete($id);
    }

    public function autoCompleteOlderThan(int $seconds): int
    {
        $orders = $this->repo->findPendingOlderThanSeconds($seconds);
        $count = 0;
        foreach ($orders as $o) {
            $this->repo->markComplete((int)$o->id);
            $count++;
        }
        return $count;
    }
}
