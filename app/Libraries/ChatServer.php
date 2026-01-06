<?php

namespace App\Libraries;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

/**
 * Standalone ChatServer for private messaging.
 * persistence is handled via HTTP fallback in the Chat controller.
 */
class ChatServer implements MessageComponentInterface
{
    protected $clients;
    protected $userConnections;
    protected $db;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->userConnections = []; // userId => [conn, conn]

        // Simple PDO for last_seen updates (standalone)
        try {
            $this->db = new \PDO("mysql:host=localhost;dbname=exam_app", "root", "");
            $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\Exception $e) {
            echo "DB Connection failed in ChatServer: " . $e->getMessage() . "\n";
        }
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "[" . date('H:i:s') . "] New connection Attempt! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);

        if (!$data)
            return;

        switch ($data['type']) {
            case 'auth':
                // Authenticate connection with userId
                $userId = $data['userId'];
                $isNewLogin = !isset($this->userConnections[$userId]) || count($this->userConnections[$userId]) === 0;

                if (!isset($this->userConnections[$userId])) {
                    $this->userConnections[$userId] = [];
                }
                $this->userConnections[$userId][] = $from;
                $from->userId = $userId;
                echo "User {$userId} authenticated on connection {$from->resourceId}\n";

                // Broadcast online status if this is their first connection
                if ($isNewLogin) {
                    $this->broadcastStatus($userId, 'online');
                }
                break;

            case 'message':
                $senderId = $data['senderId'];
                $receiverId = $data['receiverId'];
                $text = $data['message'];

                echo "Message from {$senderId} to {$receiverId}\n";

                // Send to receiver if online
                if (isset($this->userConnections[$receiverId])) {
                    foreach ($this->userConnections[$receiverId] as $conn) {
                        try {
                            $conn->send(json_encode([
                                'type' => 'message',
                                'senderId' => $senderId,
                                'message' => $text,
                                'timestamp' => date('Y-m-d H:i:s')
                            ]));
                        } catch (\Exception $e) {
                            echo "Failed to send to {$receiverId}\n";
                        }
                    }
                }

                // Echo back to sender's other connections if any
                if (isset($this->userConnections[$senderId])) {
                    foreach ($this->userConnections[$senderId] as $conn) {
                        if ($conn !== $from) {
                            $conn->send(json_encode([
                                'type' => 'message',
                                'senderId' => $senderId,
                                'message' => $text,
                                'timestamp' => date('Y-m-d H:i:s')
                            ]));
                        }
                    }
                }
                break;

            case 'typing':
                $senderId = $data['senderId'];
                $receiverId = $data['receiverId'];

                if (isset($this->userConnections[$receiverId])) {
                    foreach ($this->userConnections[$receiverId] as $conn) {
                        $conn->send(json_encode([
                            'type' => 'typing',
                            'senderId' => $senderId
                        ]));
                    }
                }
                break;
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        if (isset($conn->userId)) {
            $userId = $conn->userId;
            echo "[" . date('H:i:s') . "] User {$userId} disconnected ({$conn->resourceId})\n";
            if (isset($this->userConnections[$userId])) {
                $index = array_search($conn, $this->userConnections[$userId]);
                if ($index !== false) {
                    unset($this->userConnections[$userId][$index]);
                }

                // If no more connections for this user, they are offline
                if (count($this->userConnections[$userId]) === 0) {
                    $now = date('Y-m-d H:i:s');
                    $this->updateLastSeen($userId, $now);
                    $this->broadcastStatus($userId, 'offline', $now);
                }
            }
        } else {
            echo "[" . date('H:i:s') . "] Anonymous connection {$conn->resourceId} closed\n";
        }

        $this->clients->detach($conn);
    }

    protected function broadcastStatus($userId, $status, $lastSeen = null)
    {
        $payload = json_encode([
            'type' => 'status',
            'userId' => $userId,
            'status' => $status,
            'lastSeen' => $lastSeen
        ]);

        foreach ($this->clients as $client) {
            $client->send($payload);
        }
    }

    protected function updateLastSeen($userId, $time)
    {
        if ($this->db) {
            try {
                $stmt = $this->db->prepare("UPDATE users SET last_seen = ? WHERE id = ?");
                $stmt->execute([$time, $userId]);
            } catch (\Exception $e) {
                echo "Failed to update last_seen: " . $e->getMessage() . "\n";
            }
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}
