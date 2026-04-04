<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;

final class AuthService
{
    public function __construct(private User $userModel) {}

    public function user(): ?array
    {
        if (!isset($_SESSION['user']) || !is_array($_SESSION['user'])) {
            return null;
        }

        return $_SESSION['user'];
    }

    public function check(): bool
    {
        return $this->user() !== null;
    }

    public function register(string $fullName, string $email, string $password, string $phone = ''): bool
    {
        if ($this->userModel->findByEmail($email) !== null) {
            return false;
        }

        $this->userModel->create($fullName, $email, $password, $phone);
        $user = $this->userModel->findByEmail($email);
        if ($user === null) {
            return false;
        }

        $this->loginFromUserRow($user);

        return true;
    }

    public function attempt(string $email, string $password): bool
    {
        $user = $this->userModel->findByEmail($email);
        if ($user === null) {
            return false;
        }

        if (!password_verify($password, (string) $user['password_hash'])) {
            return false;
        }

        $this->loginFromUserRow($user);

        return true;
    }

    public function logout(): void
    {
        unset($_SESSION['user']);
    }

    private function loginFromUserRow(array $user): void
    {
        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'full_name' => (string) $user['full_name'],
            'email' => (string) $user['email'],
            'phone' => (string) ($user['phone'] ?? ''),
        ];
    }
}
