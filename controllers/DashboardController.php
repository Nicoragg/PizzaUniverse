<?php

namespace App\Controllers;

use App\Dal\Connection;
use App\Dal\PizzaDao;
use App\Dal\CustomerDao;
use App\Dal\OrderDao;
use App\Dal\UserDao;
use App\Views\DashboardView;
use \PDO;
use \PDOException;

abstract class DashboardController
{
    public static function index(): void
    {
        $stats = self::getStatistics();
        $recentOrders = self::getRecentOrders();
        $topPizzas = self::getTopPizzas();
        $monthlyRevenue = self::getMonthlyRevenue();
        $ordersByStatus = self::getOrdersByStatus();

        DashboardView::render($stats, $recentOrders, $topPizzas, $monthlyRevenue, $ordersByStatus);
    }

    private static function getStatistics(): array
    {
        try {
            $pdo = Connection::getConnection();

            // Total de pedidos
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM orders");
            $stmt->execute();
            $totalOrders = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Total de clientes
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM customers");
            $stmt->execute();
            $totalCustomers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Total de pizzas
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM pizzas");
            $stmt->execute();
            $totalPizzas = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Total de usuários
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM users");
            $stmt->execute();
            $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Receita total
            $stmt = $pdo->prepare("SELECT SUM(total_amount) as total FROM orders WHERE status != 'cancelled'");
            $stmt->execute();
            $totalRevenue = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

            // Pedidos hoje
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM orders WHERE DATE(created_at) = CURDATE()");
            $stmt->execute();
            $todayOrders = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Receita hoje
            $stmt = $pdo->prepare("SELECT SUM(total_amount) as total FROM orders WHERE DATE(created_at) = CURDATE() AND status != 'cancelled'");
            $stmt->execute();
            $todayRevenue = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

            // Clientes ativos
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM customers WHERE status = 'active'");
            $stmt->execute();
            $activeCustomers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            return [
                'totalOrders' => $totalOrders,
                'totalCustomers' => $totalCustomers,
                'totalPizzas' => $totalPizzas,
                'totalUsers' => $totalUsers,
                'totalRevenue' => $totalRevenue,
                'todayOrders' => $todayOrders,
                'todayRevenue' => $todayRevenue,
                'activeCustomers' => $activeCustomers
            ];
        } catch (\PDOException $e) {
            throw new PDOException("Erro ao buscar estatísticas: " . $e->getMessage());
        }
    }

    private static function getRecentOrders(): array
    {
        try {
            $pdo = Connection::getConnection();
            $stmt = $pdo->prepare("
                SELECT o.*, c.name as customer_name 
                FROM orders o 
                LEFT JOIN customers c ON o.customer_id = c.id 
                ORDER BY o.created_at DESC 
                LIMIT 5
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new PDOException("Erro ao buscar pedidos recentes: " . $e->getMessage());
        }
    }

    private static function getTopPizzas(): array
    {
        try {
            $pdo = Connection::getConnection();
            $stmt = $pdo->prepare("
                SELECT p.name, SUM(oi.quantity) as total_sold, SUM(oi.subtotal) as total_revenue
                FROM order_items oi
                INNER JOIN pizzas p ON oi.pizza_id = p.id
                INNER JOIN orders o ON oi.order_id = o.id
                WHERE o.status != 'cancelled'
                GROUP BY p.id, p.name
                ORDER BY total_sold DESC
                LIMIT 5
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new PDOException("Erro ao buscar pizzas mais vendidas: " . $e->getMessage());
        }
    }

    private static function getMonthlyRevenue(): array
    {
        try {
            $pdo = Connection::getConnection();
            $stmt = $pdo->prepare("
                SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') as month,
                    SUM(total_amount) as revenue,
                    COUNT(*) as orders_count
                FROM orders 
                WHERE status != 'cancelled' 
                    AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                ORDER BY month
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new PDOException("Erro ao buscar receita mensal: " . $e->getMessage());
        }
    }

    private static function getOrdersByStatus(): array
    {
        try {
            $pdo = Connection::getConnection();
            $stmt = $pdo->prepare("
                SELECT status, COUNT(*) as count
                FROM orders
                GROUP BY status
                ORDER BY count DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new PDOException("Erro ao buscar pedidos por status: " . $e->getMessage());
        }
    }
}
