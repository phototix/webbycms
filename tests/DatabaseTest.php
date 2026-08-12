<?php

declare(strict_types=1);

namespace WebbyCMS\Tests;

use PDO;
use WebbyCMS\Config;
use WebbyCMS\Db\Database;

final class DatabaseTest extends TestCase
{
    private Database $db;

    protected function setUp(): void
    {
        parent::setUp();

        if (!in_array('sqlite', PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('The sqlite PDO driver is not available.');
        }

        $this->db = new Database(new Config(), 'sqlite::memory:');
        $this->db->query('CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, email TEXT NOT NULL UNIQUE)');
    }

    public function testInsertFetch(): void
    {
        $id = $this->db->insert('users', ['name' => 'Brandon', 'email' => 'brandon@example.com']);

        $this->assertSame(1, $id);
        $this->assertSame('Brandon', $this->db->value('SELECT name FROM users WHERE id = ?', [$id]));
    }

    public function testFetchAll(): void
    {
        $this->db->insert('users', ['name' => 'A', 'email' => 'a@example.com']);
        $this->db->insert('users', ['name' => 'B', 'email' => 'b@example.com']);

        $rows = $this->db->fetchAll('SELECT * FROM users ORDER BY id');
        $this->assertCount(2, $rows);
    }

    public function testUpdate(): void
    {
        $id = $this->db->insert('users', ['name' => 'A', 'email' => 'a@example.com']);

        $affected = $this->db->update('users', ['name' => 'B'], ['id' => $id]);
        $this->assertSame(1, $affected);
        $this->assertSame('B', $this->db->value('SELECT name FROM users WHERE id = ?', [$id]));
    }

    public function testDelete(): void
    {
        $id = $this->db->insert('users', ['name' => 'A', 'email' => 'a@example.com']);

        $this->assertSame(1, $this->db->delete('users', ['id' => $id]));
        $this->assertNull($this->db->value('SELECT id FROM users WHERE id = ?', [$id]));
    }
}
