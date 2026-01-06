<?php

namespace App\Controllers;

use App\Models\MessageModel;

class Chat extends BaseController
{
    public function getContacts()
    {
        $userId = session()->get('user_id');
        $role = session()->get('role');
        $db = \Config\Database::connect();

        if ($role === 'teacher') {
            // Get class IDs for this teacher
            $classQuery = $db->table('user_classes')
                ->select('class_id')
                ->where('user_id', $userId)
                ->get()
                ->getResultArray();
            $classIds = array_column($classQuery, 'class_id');

            if (empty($classIds)) {
                return $this->response->setJSON([]);
            }

            // Find students in these classes
            $contacts = $db->table('users')
                ->select('users.id, users.full_name, users.username, "student" as role, users.last_seen')
                ->join('user_classes', 'user_classes.user_id = users.id')
                ->whereIn('user_classes.class_id', $classIds)
                ->where('users.role', 'student')
                ->groupBy('users.id')
                ->get()
                ->getResultArray();
        } elseif ($role === 'student') {
            // Get class IDs for this student
            $classQuery = $db->table('user_classes')
                ->select('class_id')
                ->where('user_id', $userId)
                ->get()
                ->getResultArray();
            $classIds = array_column($classQuery, 'class_id');

            if (empty($classIds)) {
                return $this->response->setJSON([]);
            }

            // Find teachers in these classes
            $contacts = $db->table('users')
                ->select('users.id, users.full_name, users.username, "teacher" as role, users.last_seen')
                ->join('user_classes', 'user_classes.user_id = users.id')
                ->whereIn('user_classes.class_id', $classIds)
                ->where('users.role', 'teacher')
                ->groupBy('users.id')
                ->get()
                ->getResultArray();
        } else {
            // Admin or other: see all teachers and students
            $contacts = $db->table('users')
                ->select('id, full_name, username, role, last_seen')
                ->where('id !=', $userId)
                ->get()
                ->getResultArray();
        }

        if (empty($contacts)) {
            return $this->response->setJSON([]);
        }

        // Add unread counts
        $messageModel = new MessageModel();
        $unreadCounts = $messageModel->getUnreadCounts($userId);
        $unreadMap = array_column($unreadCounts, 'count', 'sender_id');

        foreach ($contacts as &$contact) {
            $contact['unread_count'] = $unreadMap[$contact['id']] ?? 0;
        }

        return $this->response->setJSON($contacts);
    }

    public function getHistory($receiverId)
    {
        $senderId = session()->get('user_id');
        if (!$senderId)
            return $this->response->setJSON([]);

        $messageModel = new MessageModel();
        $history = $messageModel->getChatHistory($senderId, $receiverId);

        // Mark as read when history is fetched (user opening the chat)
        $messageModel->markAsRead($senderId, $receiverId);

        return $this->response->setJSON($history);
    }

    public function saveMessage()
    {
        $senderId = session()->get('user_id');
        $receiverId = $this->request->getPost('receiverId');
        $message = $this->request->getPost('message');

        if (!$senderId || !$receiverId || !$message) {
            return $this->response->setJSON(['status' => 'error']);
        }

        $messageModel = new MessageModel();
        $messageModel->insert([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'message' => $message,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['status' => 'success']);
    }
}
