<?php

namespace App\Entity;

class SecurityUser extends Entity
{
    private string $salt = '';

    private string $password = '';

    private string $token = '';

    private ?\DateTime $lastLogin = null;

    private string $role = 'user';

    /**
     * @unique
     */
    private string $login = '';

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @param string $role
     */
    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    /**
     * @return string
     */
    public function getLastLogin(): ?string
    {
        return $this->lastLogin === null ? null : $this->lastLogin->format('Y-m-d H:i:s');
    }

    /**
     * @param ?\DateTime $lastLogin
     */
    public function setLastLogin(?\DateTime $lastLogin): void
    {
        $this->lastLogin = $lastLogin;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @param string $login
     */
    public function setLogin(string $login): void
    {
        $this->login = $login;
    }

    /**
     * @return string
     */
    public function getSalt(): string
    {
        return $this->salt;
    }

    /**
     * @param string $salt
     */
    public function setSalt(string $salt): void
    {
        $this->salt = $salt;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @param bool $encrypt
     */
    public function setPassword(string $password, bool $encrypt = false): void
    {
        if($encrypt === true) {
            $this->setSalt(substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', mt_rand(1,10))), 1, 20));

            $this->password = password_hash($password.$this->salt, PASSWORD_ARGON2I);
        } else {
            $this->password = $password;
        }
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    private function checkPassword(string $password): bool
    {
        return password_verify($password.$this->getSalt(), $this->password);
    }

    private function generateToken(): void
    {
        $this->setToken(sha1(mt_rand(1, 90000) . md5(microtime())));
    }

    public function login(string $password): void
    {
        assert($this->checkPassword($password) === true, new \Exception('invalid username or password', 401));

        $this->setLastLogin(new \DateTime('now'));
        $this->generateToken();
    }
}