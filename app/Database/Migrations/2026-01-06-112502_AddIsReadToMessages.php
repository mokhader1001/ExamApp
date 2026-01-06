<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIsReadToMessages extends Migration
{
    public function up()
    {
        $fields = [
            'is_read' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'after' => 'message'
            ],
            'read_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'is_read'
            ]
        ];
        $this->forge->addColumn('messages', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('messages', ['is_read', 'read_at']);
    }
}
