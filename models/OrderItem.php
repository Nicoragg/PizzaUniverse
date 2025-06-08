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

    private array $dynamicProperties = [];

    private array $propertyMap = [
        'order_id' => 'orderId',
        'pizza_id' => 'pizzaId',
        'unit_price' => 'unitPrice',
        'created_at' => 'createdAt'
    ];

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
        // Check if property exists directly
        if (property_exists($this, $attr)) {
            return $this->$attr;
        }

        // Check if it's a snake_case version of a camelCase property
        if (isset($this->propertyMap[$attr]) && property_exists($this, $this->propertyMap[$attr])) {
            $camelCaseProperty = $this->propertyMap[$attr];
            return $this->$camelCaseProperty;
        }

        // Check dynamic properties
        if (array_key_exists($attr, $this->dynamicProperties)) {
            return $this->dynamicProperties[$attr];
        }

        throw new InvalidArgumentException("Property '{$attr}' does not exist");
    }

    public function __set(string $attr, mixed $value): void
    {
        if ($attr === 'id') {
            throw new InvalidArgumentException("Cannot modify readonly property 'id'");
        }

        // Check if property exists directly
        if (property_exists($this, $attr)) {
            $this->$attr = $value;
            return;
        }

        // Check if it's a snake_case version of a camelCase property
        if (isset($this->propertyMap[$attr]) && property_exists($this, $this->propertyMap[$attr])) {
            $camelCaseProperty = $this->propertyMap[$attr];
            $this->$camelCaseProperty = $value;
            return;
        }

        // Allow setting dynamic properties for display purposes
        $this->dynamicProperties[$attr] = $value;
    }
}
