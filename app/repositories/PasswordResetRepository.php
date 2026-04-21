<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class PasswordResetRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function createToken(string $email, string $token, string $expiresAt): bool
    {
        $this->deleteByEmail($email);

        $stmt = $this->db->prepare(
            "INSERT INTO password_resets (email, token, expires_at)
             VALUES (:email, :token, :expires_at)"
        );

        return $stmt->execute([
            'email' => $email,
            'token' => $token,
            'expires_at' => $expiresAt,
        ]);
    }

    public function findByToken(string $token): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM password_resets WHERE token = :token LIMIT 1");
        $stmt->execute(['token' => $token]);
        return $stmt->fetch();
    }

    public function deleteByEmail(string $email): bool
    {
        $stmt = $this->db->prepare("DELETE FROM password_resets WHERE email = :email");
        return $stmt->execute(['email' => $email]);
    }

    public function deleteByToken(string $token): bool
    {
        $stmt = $this->db->prepare("DELETE FROM password_resets WHERE token = :token");
        return $stmt->execute(['token' => $token]);
    }
}
