<?php

namespace App\Models;

use \InvalidArgumentException;

class OrderItem
{
    private readonly int $id;
    private int $orderId;
    private int $pizzaId;
    private int $quantity;
    private float $unitPrice;
    private float $subtotal;
    private ?string $notes;
    private string $createdAt;

    public function __construct(
        int $id,
        int $orderId = 0,
        int $pizzaId = 0,
        int $quantity = 1,
        float $unitPrice = 0.0,
        float $subtotal = 0.0,
        ?string $notes = null,
        string $createdAt = ""
    ) {
        $this->id = $id;
        $this->orderId = $orderId;
        $this->pizzaId = $pizzaId;
        $this->quantity = $quantity;
        $this->unitPrice = $unitPrice;
        $this->subtotal = $subtotal;
        $this->notes = $notes;
        $this->createdAt = $createdAt;
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
