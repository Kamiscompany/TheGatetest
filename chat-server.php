<?php
// chat-server.php

require dirname(__DIR__) . '/vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);

        include('includes/db.php');

        $stmt = $pdo->prepare('INSERT INTO group_messages (group_id, user_id, message, sent_at) VALUES (?, ?, ?, NOW())');
        $stmt->execute([$data['group_id'], $data['user_id'], $data['message']]);

        $userStmt = $pdo->prepare('SELECT username FROM users WHERE id = ?');
        $userStmt->execute([$data['user_id']]);
        $username = $userStmt->fetchColumn();

        $msg = json_encode([
            'username' => $username,
            'message' => $data['message'],
            'sent_at' => date('Y-m-d H:i:s')
        ]);

        foreach ($this->clients as $client) {
            if ($from !== $client) {
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat()
        )
    ),
    8080
);

$server->run();
?>