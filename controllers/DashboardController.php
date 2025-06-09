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
        $dailyAnalytics = self::getDailyAnalytics();
        $weeklyComparison = self::getWeeklyComparison();
        $customerAnalytics = self::getCustomerAnalytics();
        $pizzaPerformance = self::getPizzaPerformance();

        DashboardView::render($stats, $recentOrders, $topPizzas, $monthlyRevenue, $ordersByStatus, $dailyAnalytics, $weeklyComparison, $customerAnalytics, $pizzaPerformance);
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

    private static function getDailyAnalytics(): array
    {
        try {
            $pdo = Connection::getConnection();
            $stmt = $pdo->prepare("
                SELECT 
                    DATE(created_at) as date,
                    COUNT(*) as orders_count,
                    SUM(total_amount) as revenue,
                    AVG(total_amount) as avg_order_value,
                    COUNT(DISTINCT customer_id) as unique_customers
                FROM orders 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                    AND status != 'cancelled'
                GROUP BY DATE(created_at)
                ORDER BY date DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new PDOException("Erro ao buscar análise diária: " . $e->getMessage());
        }
    }

    private static function getWeeklyComparison(): array
    {
        try {
            $pdo = Connection::getConnection();
            $stmt = $pdo->prepare("
                SELECT 
                    'current_week' as period,
                    COUNT(*) as orders_count,
                    SUM(total_amount) as revenue,
                    AVG(total_amount) as avg_order_value
                FROM orders 
                WHERE YEARWEEK(created_at, 1) = YEARWEEK(NOW(), 1)
                    AND status != 'cancelled'
                UNION ALL
                SELECT 
                    'previous_week' as period,
                    COUNT(*) as orders_count,
                    SUM(total_amount) as revenue,
                    AVG(total_amount) as avg_order_value
                FROM orders 
                WHERE YEARWEEK(created_at, 1) = YEARWEEK(DATE_SUB(NOW(), INTERVAL 1 WEEK), 1)
                    AND status != 'cancelled'
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new PDOException("Erro ao buscar comparação semanal: " . $e->getMessage());
        }
    }

    private static function getCustomerAnalytics(): array
    {
        try {
            $pdo = Connection::getConnection();

            // Novos clientes por mês
            $stmt = $pdo->prepare("
                SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') as month,
                    COUNT(*) as new_customers
                FROM customers 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                ORDER BY month
            ");
            $stmt->execute();
            $newCustomers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Clientes recorrentes
            $stmt = $pdo->prepare("
                SELECT 
                    c.id,
                    c.name,
                    COUNT(o.id) as total_orders,
                    SUM(o.total_amount) as total_spent,
                    MAX(o.created_at) as last_order
                FROM customers c
                INNER JOIN orders o ON c.id = o.customer_id
                WHERE o.status != 'cancelled'
                GROUP BY c.id, c.name
                HAVING total_orders >= 3
                ORDER BY total_spent DESC
                LIMIT 10
            ");
            $stmt->execute();
            $recurringCustomers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'newCustomers' => $newCustomers,
                'recurringCustomers' => $recurringCustomers
            ];
        } catch (\PDOException $e) {
            throw new PDOException("Erro ao buscar análise de clientes: " . $e->getMessage());
        }
    }

    private static function getPizzaPerformance(): array
    {
        try {
            $pdo = Connection::getConnection();
            $stmt = $pdo->prepare("
                SELECT 
                    p.id,
                    p.name,
                    p.price,
                    COALESCE(SUM(oi.quantity), 0) as total_sold,
                    COALESCE(SUM(oi.subtotal), 0) as total_revenue,
                    COALESCE(AVG(oi.quantity), 0) as avg_quantity_per_order,
                    (SELECT COUNT(*) FROM order_items oi2 WHERE oi2.pizza_id = p.id) as order_frequency
                FROM pizzas p
                LEFT JOIN order_items oi ON p.id = oi.pizza_id
                LEFT JOIN orders o ON oi.order_id = o.id AND o.status != 'cancelled'
                GROUP BY p.id, p.name, p.price
                ORDER BY total_revenue DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new PDOException("Erro ao buscar performance das pizzas: " . $e->getMessage());
        }
    }
}
