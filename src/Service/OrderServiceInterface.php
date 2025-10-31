<?php
namespace App\Service;

interface OrderServiceInterface
{
    public function createOrder(array $data): array;
    public function listActive(): array;
    public function completeOrder(int $id): bool;
    public function autoCompleteOlderThan(int $seconds): int;
}
