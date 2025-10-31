<?php
require __DIR__ . '/../../vendor/autoload.php';
use App\Db\PDOSingleton;
use App\Repository\OrderRepository;
use App\Service\OrderService;

$settings = require __DIR__ . '/../Settings.php';
PDOSingleton::init($settings['settings']);
$pdo = PDOSingleton::getInstance()->getConnection();

$repo = new OrderRepository($pdo);
$service = new OrderService($repo, $settings['settings']['kitchen_capacity']);

$seconds = $settings['settings']['auto_complete_seconds'] ?? 120;
if (!$seconds) {
    echo "Auto-complete disabled. Set auto_complete_seconds in Settings.php\n";
    exit;
}

// Simple long-running loop; for production use supervisor or a queue worker
while (true) {
    $count = $service->autoCompleteOlderThan((int)$seconds);
    if ($count > 0) {
        echo "[".date('Y-m-d H:i:s')."] auto-completed {$count} orders\n";
    }
    sleep(30); // poll interval
}