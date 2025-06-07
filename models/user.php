<?php

namespace App\Models;

use \InvalidArgumentException;

class User
{
    private readonly int $id;
    private string $username;
    private string $email;
    private string $password;

    public function __construct(int $id, string $username = "", string $email = "", string $password = "")
    {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
    }

    public function __get(string $attr): mixed
    {
        if (property_exists($this, $attr)) {
            return $this->$attr;
        }

        throw new InvalidArgumentException("Property '{$attr}' does not exist");
    }

    public function __set(string $attr, mixed $value): void
    {
        if ($attr === 'id') {
            throw new InvalidArgumentException("Cannot modify readonly property 'id'");
        }

        if (property_exists($this, $attr)) {
            $this->$attr = $value;
        } else {
            throw new InvalidArgumentException("Property '{$attr}' does not exist");
        }
    }
}
