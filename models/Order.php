<?php

namespace App\Models;

use \InvalidArgumentException;

class Order
{
    private readonly int $id;
    private int $customerId;
    private string $orderNumber;
    private string $status;
    private float $totalAmount;
    private ?string $deliveryAddress;
    private ?string $notes;
    private string $createdAt;
    private string $updatedAt;

    public function __construct(
        int $id,
        int $customerId = 0,
        string $orderNumber = "",
        string $status = "pending",
        float $totalAmount = 0.0,
        ?string $deliveryAddress = null,
        ?string $notes = null,
        string $createdAt = "",
        string $updatedAt = ""
    ) {
        $this->id = $id;
        $this->customerId = $customerId;
        $this->orderNumber = $orderNumber;
        $this->status = $status;
        $this->totalAmount = $totalAmount;
        $this->deliveryAddress = $deliveryAddress;
        $this->notes = $notes;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
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
