<?php

namespace App\Models;

use CodeIgniter\Model;

class MessageModel extends Model
{
    protected $table = 'messages';
    protected $primaryKey = 'id';
    protected $allowedFields = ['sender_id', 'receiver_id', 'message', 'created_at', 'is_read', 'read_at'];
    protected $useTimestamps = false; // We use database default for created_at

    public function getChatHistory($user1, $user2)
    {
        return $this->where("(sender_id = $user1 AND receiver_id = $user2)")
            ->orWhere("(sender_id = $user2 AND receiver_id = $user1)")
            ->orderBy('created_at', 'ASC')
            ->findAll();
    }

    public function markAsRead($userId, $otherId)
    {
        return $this->where('receiver_id', $userId)
            ->where('sender_id', $otherId)
            ->where('is_read', 0)
            ->set(['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')])
            ->update();
    }

    public function getUnreadCounts($userId)
    {
        return $this->select('sender_id, COUNT(*) as count')
            ->where('receiver_id', $userId)
            ->where('is_read', 0)
            ->groupBy('sender_id')
            ->findAll();
    }
}
