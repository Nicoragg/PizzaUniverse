<?php

namespace App\Models;

use \InvalidArgumentException;

class Customer
{
    private readonly int $id;
    private string $name;
    private string $cpf;
    private string $phone;
    private string $status;
    private string $zipcode;
    private string $neighborhood;
    private string $street;
    private string $city;
    private string $state;

    public function __construct(
        int $id,
        string $name = "",
        string $cpf = "",
        string $phone = "",
        string $status = "active",
        string $zipcode = "",
        string $neighborhood = "",
        string $street = "",
        string $city = "",
        string $state = ""
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->cpf = $cpf;
        $this->phone = $phone;
        $this->status = $status;
        $this->zipcode = $zipcode;
        $this->neighborhood = $neighborhood;
        $this->street = $street;
        $this->city = $city;
        $this->state = $state;
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
