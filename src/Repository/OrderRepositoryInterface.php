<?php
namespace App\Repository;

use App\Model\OrderModel;

interface OrderRepositoryInterface
{
    public function create(OrderModel $order): int;
    public function countActive(): int;
    public function findActive(): array;
    public function findById(int $id): ?OrderModel;
    public function markComplete(int $id): bool;
    public function findPendingOlderThanSeconds(int $seconds): array;
}
