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

    private array $dynamicProperties = [];

    private array $propertyMap = [
        'customer_id' => 'customerId',
        'order_number' => 'orderNumber',
        'total_amount' => 'totalAmount',
        'delivery_address' => 'deliveryAddress',
        'created_at' => 'createdAt',
        'updated_at' => 'updatedAt'
    ];

    private static array $validTransitions = [
        'pending' => ['confirmed', 'cancelled'],
        'confirmed' => ['preparing', 'cancelled'],
        'preparing' => ['ready', 'cancelled'],
        'ready' => ['delivered'],
        'delivered' => [],
        'cancelled' => []
    ];

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

        if (isset($this->propertyMap[$attr]) && property_exists($this, $this->propertyMap[$attr])) {
            $camelCaseProperty = $this->propertyMap[$attr];
            return $this->$camelCaseProperty;
        }

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

        if (property_exists($this, $attr)) {
            $this->$attr = $value;
            return;
        }

        if (isset($this->propertyMap[$attr]) && property_exists($this, $this->propertyMap[$attr])) {
            $camelCaseProperty = $this->propertyMap[$attr];
            $this->$camelCaseProperty = $value;
            return;
        }

        $this->dynamicProperties[$attr] = $value;
    }

    public function canTransitionTo(string $newStatus): bool
    {
        return in_array($newStatus, self::$validTransitions[$this->status] ?? []);
    }

    public function getAvailableTransitions(): array
    {
        return self::$validTransitions[$this->status] ?? [];
    }
}
