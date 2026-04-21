<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class UserRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function findByEmail(string $email): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO users (name, email, password, role)
             VALUES (:name, :email, :password, :role)"
        );

        return $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'],
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE users SET name = :name, email = :email, role = :role";
        $params = [
            'id' => $id,
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
        ];

        if (!empty($data['password'])) {
            $sql .= ", password = :password";
            $params['password'] = $data['password'];
        }

        $sql .= " WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function findByEmailExceptId(string $email, int $excludeId): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email AND id != :id LIMIT 1");
        $stmt->execute(['email' => $email, 'id' => $excludeId]);
        return $stmt->fetch();
    }

    public function all(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM users ORDER BY name ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function totalCount(): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM users");
        $stmt->execute();
        $row = $stmt->fetch();
        return (int)($row['total'] ?? 0);
    }
}