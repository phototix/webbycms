<?php

declare(strict_types=1);

namespace WebbyCMS\Db;

use PDO;
use WebbyCMS\Config;

/**
 * Optional PDO database wrapper (MySQL by default).
 *
 * Use $db->query($sql, $params) for prepared statements. The connection is
 * created lazily on first use and only when DB_ENABLED=true.
 */
final class Database
{
    private ?PDO $pdo = null;

    public function __construct(
        private readonly Config $config,
        private readonly ?string $dsnOverride = null,
    ) {
    }

    public function connect(): PDO
    {
        if ($this->pdo !== null) {
            return $this->pdo;
        }

        $dsn = $this->dsnOverride ?? sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            (string) $this->config->get('DB_HOST', '127.0.0.1'),
            $this->config->int('DB_PORT', 3306),
            (string) $this->config->get('DB_NAME', ''),
        );

        $this->pdo = new PDO(
            $dsn,
            (string) $this->config->get('DB_USER', ''),
            (string) $this->config->get('DB_PASSWORD', ''),
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ],
        );

        return $this->pdo;
    }

    public function pdo(): PDO
    {
        return $this->connect();
    }

    public function query(string $sql, array $params = []): \PDOStatement
    {
        $statement = $this->pdo()->prepare($sql);
        $statement->execute($params);

        return $statement;
    }

    public function fetch(string $sql, array $params = []): ?array
    {
        $row = $this->query($sql, $params)->fetch();

        return $row === false ? null : $row;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function value(string $sql, array $params = []): mixed
    {
        $row = $this->fetch($sql, $params);

        if ($row === null) {
            return null;
        }

        return reset($row);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function insert(string $table, array $data): int|string
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $this->query("INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})", array_values($data));

        return (int) $this->pdo()->lastInsertId();
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $where
     */
    public function update(string $table, array $data, array $where): int
    {
        $set = implode(' = ?, ', array_keys($data)) . ' = ?';
        $conditions = $this->conditions($where);
        $params = array_merge(array_values($data), array_values($where));

        $statement = $this->query("UPDATE {$table} SET {$set} WHERE {$conditions}", $params);

        return $statement->rowCount();
    }

    /**
     * @param array<string, mixed> $where
     */
    public function delete(string $table, array $where): int
    {
        $conditions = $this->conditions($where);

        $statement = $this->query("DELETE FROM {$table} WHERE {$conditions}", array_values($where));

        return $statement->rowCount();
    }

    /**
     * @param array<string, mixed> $where
     */
    private function conditions(array $where): string
    {
        return implode(' AND ', array_map(static fn (string $column): string => "{$column} = ?", array_keys($where)));
    }
}
