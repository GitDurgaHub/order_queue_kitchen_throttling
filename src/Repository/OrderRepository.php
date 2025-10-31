<?php
namespace App\Repository;

use PDO;
use App\Model\OrderModel;

class OrderRepository implements OrderRepositoryInterface
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(OrderModel $order): int
    {
        $stmt = $this->pdo->prepare("INSERT INTO orders (items, vip, pickup_time, status, created_at) VALUES (?, ?, ?, 'active', NOW())");
        $stmt->execute([json_encode($order->items, JSON_UNESCAPED_UNICODE), (int)$order->vip, $order->pickup_time]);
        return (int)$this->pdo->lastInsertId();
    }

    public function countActive(): int
    {
        return (int)$this->pdo->query("SELECT COUNT(*) FROM orders WHERE status='active'")->fetchColumn();
    }

    public function findActive(): array
    {
        $stmt = $this->pdo->query("SELECT id, items, vip, pickup_time, created_at FROM orders WHERE status='active' ORDER BY created_at ASC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function ($r) {
            return new OrderModel($r);
        }, $rows);
    }

    public function findById(int $id): ?OrderModel
    {
        $stmt = $this->pdo->prepare("SELECT id, items FROM orders WHERE id = ? AND status = 'active'");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new OrderModel($data) : null;
    }

    public function markComplete(int $id): bool
    {
        $stmt = $this->pdo->prepare("UPDATE orders SET status = 'completed' WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function findPendingOlderThanSeconds(int $seconds): array
    {
        $stmt = $this->pdo->prepare("SELECT items, vip, pickup_time, created_at FROM orders WHERE status = 'active' AND created_at < (NOW() - INTERVAL ? SECOND)");
        $stmt->execute([$seconds]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function ($r) {
            return new OrderModel($r);
        }, $rows);
    }
}
