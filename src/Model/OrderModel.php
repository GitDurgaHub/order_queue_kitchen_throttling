<?php
namespace App\Model;

class OrderModel
{
    public $id;
    public $items;
    public $vip;
    public $pickup_time;
    public $status;
    public $created_at;
    public $completed_at;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->items = isset($data['items']) ? (is_array($data['items']) ? $data['items'] : json_decode($data['items'], true) ?? []) : [];
        $this->vip = !empty($data['VIP']);
        $this->pickup_time = $data['pickup_time'] ?? date('Y-m-d H:i:s');
        $this->status = $data['status'] ?? 'active';
        $this->created_at = $data['created_at'] ?? null;
        $this->completed_at = $data['completed_at'] ?? null;
    }
}