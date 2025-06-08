<?php

namespace App\Models;

use \InvalidArgumentException;

class Pizza
{
    private readonly int $id;
    private string $name;
    private string $description;
    private float $price;
    private string $category;

    public function __construct(int $id, string $name = "", string $description = "", float $price = 0.0, string $category = "")
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->category = $category;
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
