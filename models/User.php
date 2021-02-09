<?php

class User
{
    public int $id;
    public string $name;
    public string $lastname;
    public string $email;
    public string $password;
    public $image;
    public $bio;
    public string $token;

    public function generateToken(): string
    {
        return bin2hex(random_bytes(50));
    }

    public function generatePassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function getFullName(User $user): string
    {
        return "$user->name $user->lastname";
    }

    public function imageGenerateName()
    {
        return bin2hex(random_bytes(60)) . ".jpg";
    }
}

interface IUser
{
    public function buildUser($data);
    public function create(User $user, $authUser = false);
    public function update(User $user, $redirect = true);
    public function verifyToken($protected = false);
    public function setTokenToSession(string $token, $redirect = true);
    public function authenticateUser(string $email, string $password);
    public function findByEmail(string $email);
    public function findById(int $id);
    public function findByToken(string $token);
    public function changePassword(User $user);
    public function destroyToken();
}
