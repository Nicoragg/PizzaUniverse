<?php

namespace App\Dal;

use App\Dal\Connection;
use App\Models\Order;
use App\Models\OrderItem;
use Exception;
use \PDO;
use \PDOException;

abstract class OrderDao
{
    public static function create(Order $order, array $orderItems): int
    {
        try {
            $pdo = Connection::getConnection();
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO orders (customer_id, order_number, status, total_amount, delivery_address, notes) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $order->customerId,
                $order->orderNumber,
                $order->status,
                $order->totalAmount,
                $order->deliveryAddress,
                $order->notes
            ]);

            $orderId = (int) $pdo->lastInsertId();

            foreach ($orderItems as $item) {
                $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, pizza_id, quantity, unit_price, subtotal, notes) VALUES (?, ?, ?, ?, ?, ?)");
                $itemStmt->execute([
                    $orderId,
                    $item->pizzaId,
                    $item->quantity,
                    $item->unitPrice,
                    $item->subtotal,
                    $item->notes
                ]);
            }

            $pdo->commit();
            return $orderId;
        } catch (\PDOException $e) {
            $pdo->rollback();
            throw new \PDOException("Erro ao salvar pedido no Banco de Dados: " . $e->getMessage());
        }
    }

    public static function findAll(): array
    {
        try {
            $pdo = Connection::getConnection();
            $stmt = $pdo->prepare("SELECT o.*, c.name as customer_name FROM orders o LEFT JOIN customers c ON o.customer_id = c.id ORDER BY o.created_at DESC");
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $orders = [];
            foreach ($res as $data) {
                $order = new Order(
                    (int) $data["id"],
                    (int) $data["customer_id"],
                    $data["order_number"],
                    $data["status"],
                    (float) $data["total_amount"],
                    $data["delivery_address"],
                    $data["notes"],
                    $data["created_at"],
                    $data["updated_at"]
                );
                // Adicionar o nome do cliente como propriedade dinâmica
                $order->customer_name = $data["customer_name"];
                $orders[] = $order;
            }

            return $orders;
        } catch (\PDOException $e) {
            throw new PDOException("Erro ao listar pedidos: " . $e->getMessage());
        }
    }

    public static function findById(int $id): ?Order
    {
        try {
            $pdo = Connection::getConnection();
            $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
            $stmt->execute([$id]);

            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$data) return null;

            return new Order(
                (int) $data["id"],
                (int) $data["customer_id"],
                $data["order_number"],
                $data["status"],
                (float) $data["total_amount"],
                $data["delivery_address"],
                $data["notes"],
                $data["created_at"],
                $data["updated_at"]
            );
        } catch (\PDOException $e) {
            throw new PDOException("Erro ao buscar pedido: " . $e->getMessage());
        }
    }

    public static function findByCustomerId(int $customerId): array
    {
        try {
            $pdo = Connection::getConnection();
            $stmt = $pdo->prepare("SELECT * FROM orders WHERE customer_id = ? ORDER BY created_at DESC");
            $stmt->execute([$customerId]);
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $orders = [];
            foreach ($res as $data) {
                $orders[] = new Order(
                    (int) $data["id"],
                    (int) $data["customer_id"],
                    $data["order_number"],
                    $data["status"],
                    (float) $data["total_amount"],
                    $data["delivery_address"],
                    $data["notes"],
                    $data["created_at"],
                    $data["updated_at"]
                );
            }

            return $orders;
        } catch (\PDOException $e) {
            throw new PDOException("Erro ao buscar pedidos do cliente: " . $e->getMessage());
        }
    }

    public static function findByStatus(string $status): array
    {
        try {
            $pdo = Connection::getConnection();
            $stmt = $pdo->prepare("SELECT * FROM orders WHERE status = ? ORDER BY created_at DESC");
            $stmt->execute([$status]);
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $orders = [];
            foreach ($res as $data) {
                $orders[] = new Order(
                    (int) $data["id"],
                    (int) $data["customer_id"],
                    $data["order_number"],
                    $data["status"],
                    (float) $data["total_amount"],
                    $data["delivery_address"],
                    $data["notes"],
                    $data["created_at"],
                    $data["updated_at"]
                );
            }

            return $orders;
        } catch (\PDOException $e) {
            throw new PDOException("Erro ao buscar pedidos por status: " . $e->getMessage());
        }
    }

    public static function getOrderItems(int $orderId): array
    {
        try {
            $pdo = Connection::getConnection();
            $stmt = $pdo->prepare("SELECT oi.*, p.name as pizza_name FROM order_items oi LEFT JOIN pizzas p ON oi.pizza_id = p.id WHERE oi.order_id = ? ORDER BY oi.id");
            $stmt->execute([$orderId]);
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $items = [];
            foreach ($res as $data) {
                $item = new OrderItem(
                    (int) $data["id"],
                    (int) $data["order_id"],
                    (int) $data["pizza_id"],
                    (int) $data["quantity"],
                    (float) $data["unit_price"],
                    (float) $data["subtotal"],
                    $data["notes"],
                    $data["created_at"]
                );
                // Adicionar o nome da pizza como propriedade dinâmica
                $item->pizza_name = $data["pizza_name"];
                $items[] = $item;
            }

            return $items;
        } catch (\PDOException $e) {
            throw new PDOException("Erro ao buscar itens do pedido: " . $e->getMessage());
        }
    }

    public static function update(Order $order): void
    {
        try {
            $pdo = Connection::getConnection();
            $stmt = $pdo->prepare("UPDATE orders SET status = ?, total_amount = ?, delivery_address = ?, notes = ? WHERE id = ?");
            $stmt->execute([
                $order->status,
                $order->totalAmount,
                $order->deliveryAddress,
                $order->notes,
                $order->id
            ]);
        } catch (\PDOException $e) {
            throw new PDOException("Erro ao alterar pedido: " . $e->getMessage());
        }
    }

    public static function updateStatus(int $orderId, string $status): void
    {
        try {
            $pdo = Connection::getConnection();
            $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $stmt->execute([$status, $orderId]);
        } catch (\PDOException $e) {
            throw new PDOException("Erro ao atualizar status do pedido: " . $e->getMessage());
        }
    }

    public static function delete(int $id): void
    {
        try {
            $pdo = Connection::getConnection();
            $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
            $stmt->execute([$id]);

            if ($stmt->rowCount() === 0) {
                throw new Exception("Nenhum registro foi excluído ou pedido não encontrado");
            }
        } catch (\PDOException $e) {
            throw new PDOException("Erro ao excluir pedido: " . $e->getMessage());
        }
    }

    public static function generateOrderNumber(): string
    {
        return 'PED' . date('Ymd') . str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);
    }

    public static function getOrderStatuses(): array
    {
        return [
            'pending' => 'Pendente',
            'confirmed' => 'Confirmado',
            'preparing' => 'Preparando',
            'ready' => 'Pronto',
            'delivered' => 'Entregue',
            'cancelled' => 'Cancelado'
        ];
    }
}
