<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class Task extends Migration
{
    public function up(){
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'null' => false,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null' => false,
            ],
            'description' => [
                'type' => 'TEXT',
                'constraint' => '150',
                'null' => true,
            ],
            'checked' => [
                'type' => 'BOOLEAN',
                'null' => false,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            // 'deleted_at' => [
            //     'type' => 'TIMESTAMP',
            //     'default' => new RawSql('CURRENT_TIMESTAMP'),
            // ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tasks');
    }

    public function down(){
        $this->forge->dropTable('tasks');
    }
}