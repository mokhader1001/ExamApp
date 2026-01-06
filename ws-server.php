<?php

/**
 * Standalone WebSocket Server
 * Run this from the project root: C:\xampp\php\php.exe ws-server.php
 */

require __DIR__ . '/vendor/autoload.php';

// Manually require the ChatServer since we are not bootstrapping the full CI4 autoloader
require_once __DIR__ . '/app/Libraries/ChatServer.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\Libraries\ChatServer;

$port = 8282;

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new ChatServer()
        )
    ),
    $port
);

echo "-------------------------------------------\n";
echo " WebSocket Server Started Successfully!\n";
echo " Listening on port: {$port}\n";
echo "-------------------------------------------\n";

$server->run();
