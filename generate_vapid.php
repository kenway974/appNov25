<?php
require __DIR__ . '/vendor/autoload.php';

use Minishlink\WebPush\VAPID;

// Generate VAPID keys
$keys = VAPID::createVapidKeys();

echo "Public key: " . $keys['publicKey'] . PHP_EOL;
echo "Private key: " . $keys['privateKey'] . PHP_EOL;
