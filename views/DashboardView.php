<?php

namespace App\Views;

abstract class DashboardView
{
    public static function render(
        array $stats,
        array $recentOrders,
        array $topPizzas,
        array $monthlyRevenue,
        array $ordersByStatus,
        array $dailyAnalytics,
        array $weeklyComparison,
        array $customerAnalytics,
        array $pizzaPerformance
    ): void {
        $statusLabels = [
            'pending' => 'Pendente',
            'confirmed' => 'Confirmado',
            'preparing' => 'Preparando',
            'ready' => 'Pronto',
            'delivered' => 'Entregue',
            'cancelled' => 'Cancelado'
        ];
?>
        <main class="dashboard-container">
            <div class="dashboard-header">
                <h1><i class="bi bi-speedometer2"></i> Dashboard - Pizza Universe</h1>
                <p class="dashboard-subtitle">Visão geral do sistema em tempo real</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card orders">
                    <div class="stat-icon">
                        <i class="bi bi-cart-check"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?= number_format($stats['totalOrders']) ?></div>
                        <div class="stat-label">Total de Pedidos</div>
                        <div class="stat-detail">
                            <span class="stat-badge today"><?= $stats['todayOrders'] ?> hoje</span>
                        </div>
                    </div>
                    <div class="stat-trend">
                        <i class="bi bi-arrow-up-right"></i>
                    </div>
                </div>

                <div class="stat-card customers">
                    <div class="stat-icon">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?= number_format($stats['totalCustomers']) ?></div>
                        <div class="stat-label">Total de Clientes</div>
                        <div class="stat-detail">
                            <span class="stat-badge active"><?= $stats['activeCustomers'] ?> ativos</span>
                        </div>
                    </div>
                    <div class="stat-trend">
                        <i class="bi bi-arrow-up-right"></i>
                    </div>
                </div>

                <div class="stat-card pizzas">
                    <div class="stat-icon">
                        🍕
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?= number_format($stats['totalPizzas']) ?></div>
                        <div class="stat-label">Pizzas Cadastradas</div>
                        <div class="stat-detail">
                            <span class="stat-badge menu">No cardápio</span>
                        </div>
                    </div>
                    <div class="stat-trend">
                        <i class="bi bi-graph-up"></i>
                    </div>
                </div>

                <div class="stat-card revenue">
                    <div class="stat-icon">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">R$ <?= number_format($stats['totalRevenue'], 2, ',', '.') ?></div>
                        <div class="stat-label">Receita Total</div>
                        <div class="stat-detail">
                            <span class="stat-badge revenue-today">R$ <?= number_format($stats['todayRevenue'], 2, ',', '.') ?> hoje</span>
                        </div>
                    </div>
                    <div class="stat-trend">
                        <i class="bi bi-arrow-up-right"></i>
                    </div>
                </div>
            </div>

            <div class="secondary-stats">
                <div class="stat-card-small users">
                    <i class="bi bi-person-gear"></i>
                    <div>
                        <h4><?= $stats['totalUsers'] ?></h4>
                        <p>Usuários do Sistema</p>
                    </div>
                </div>

                <div class="stat-card-small quick-actions">
                    <i class="bi bi-lightning"></i>
                    <div>
                        <h4>Ações Rápidas</h4>
                        <div class="quick-links">
                            <a href="?page=orders&action=create" class="quick-link">
                                <i class="bi bi-plus-circle"></i> Novo Pedido
                            </a>
                            <a href="?page=customers&action=create" class="quick-link">
                                <i class="bi bi-person-plus"></i> Novo Cliente
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dashboard-grid">
                <div class="dashboard-card recent-orders">
                    <div class="card-header">
                        <h3><i class="bi bi-clock-history"></i> Pedidos Recentes</h3>
                        <a href="?page=orders" class="view-all">Ver todos</a>
                    </div>
                    <div class="card-content">
                        <?php if (!empty($recentOrders)): ?>
                            <div class="orders-list">
                                <?php foreach ($recentOrders as $order): ?>
                                    <div class="order-item">
                                        <div class="order-info">
                                            <strong><?= htmlspecialchars($order['order_number']) ?></strong>
                                            <span class="customer"><?= htmlspecialchars($order['customer_name'] ?? 'Cliente não encontrado') ?></span>
                                        </div>
                                        <div class="order-details">
                                            <span class="status status-<?= $order['status'] ?>">
                                                <?= $statusLabels[$order['status']] ?? $order['status'] ?>
                                            </span>
                                            <span class="amount">R$ <?= number_format($order['total_amount'], 2, ',', '.') ?></span>
                                        </div>
                                        <div class="order-date">
                                            <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <p>Nenhum pedido encontrado</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="dashboard-card top-pizzas">
                    <div class="card-header">
                        <h3><i class="bi bi-trophy"></i> Pizzas Mais Vendidas</h3>
                        <a href="?page=pizzas" class="view-all">Gerenciar</a>
                    </div>
                    <div class="card-content">
                        <?php if (!empty($topPizzas)): ?>
                            <div class="pizza-ranking">
                                <?php foreach ($topPizzas as $index => $pizza): ?>
                                    <div class="pizza-rank-item">
                                        <div class="rank-position"><?= $index + 1 ?>º</div>
                                        <div class="pizza-info">
                                            <strong><?= htmlspecialchars($pizza['name']) ?></strong>
                                            <span class="pizza-stats">
                                                <?= $pizza['total_sold'] ?> vendidas •
                                                R$ <?= number_format($pizza['total_revenue'], 2, ',', '.') ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="bi bi-pizza"></i>
                                <p>Nenhuma venda registrada</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="dashboard-card orders-status">
                    <div class="card-header">
                        <h3><i class="bi bi-pie-chart"></i> Status dos Pedidos</h3>
                    </div>
                    <div class="card-content">
                        <?php if (!empty($ordersByStatus)): ?>
                            <div class="status-chart">
                                <?php foreach ($ordersByStatus as $statusData): ?>
                                    <div class="status-item">
                                        <div class="status-label">
                                            <span class="status-dot status-<?= $statusData['status'] ?>"></span>
                                            <?= $statusLabels[$statusData['status']] ?? $statusData['status'] ?>
                                        </div>
                                        <div class="status-count"><?= $statusData['count'] ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="bi bi-bar-chart"></i>
                                <p>Nenhum dado disponível</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Seção de Análise Avançada - 100% da largura -->
            <div class="analytics-section">
                <div class="analytics-header">
                    <h2><i class="bi bi-graph-up-arrow"></i>Análise Avançada</h2>
                    <p>Insights detalhados sobre performance e tendências do negócio</p>
                </div>

                <!-- Comparação Semanal -->
                <div class="analytics-row">
                    <div class="analytics-card weekly-comparison">
                        <div class="card-header">
                            <h3><i class="bi bi-calendar-week"></i> Comparação Semanal</h3>
                            <span class="period-indicator">Esta semana vs. Semana anterior</span>
                        </div>
                        <div class="card-content">
                            <?php
                            $currentWeek = null;
                            $previousWeek = null;
                            foreach ($weeklyComparison as $week) {
                                if ($week['period'] === 'current_week') {
                                    $currentWeek = $week;
                                } else {
                                    $previousWeek = $week;
                                }
                            }
                            ?>
                            <div class="comparison-grid">
                                <div class="comparison-metric">
                                    <div class="metric-icon">
                                        <i class="bi bi-cart-check"></i>
                                    </div>
                                    <div class="metric-content">
                                        <div class="metric-values">
                                            <span class="current-value"><?= $currentWeek['orders_count'] ?? 0 ?></span>
                                            <span class="previous-value">vs. <?= $previousWeek['orders_count'] ?? 0 ?></span>
                                        </div>
                                        <div class="metric-label">Pedidos</div>
                                        <?php
                                        $ordersChange = 0;
                                        if (($previousWeek['orders_count'] ?? 0) > 0) {
                                            $ordersChange = ((($currentWeek['orders_count'] ?? 0) - ($previousWeek['orders_count'] ?? 0)) / ($previousWeek['orders_count'] ?? 1)) * 100;
                                        }
                                        ?>
                                        <div class="metric-change <?= $ordersChange >= 0 ? 'positive' : 'negative' ?>">
                                            <i class="bi bi-arrow-<?= $ordersChange >= 0 ? 'up' : 'down' ?>"></i>
                                            <?= abs(number_format($ordersChange, 1)) ?>%
                                        </div>
                                    </div>
                                </div>

                                <div class="comparison-metric">
                                    <div class="metric-icon">
                                        <i class="bi bi-currency-dollar"></i>
                                    </div>
                                    <div class="metric-content">
                                        <div class="metric-values">
                                            <span class="current-value">R$ <?= number_format($currentWeek['revenue'] ?? 0, 0, ',', '.') ?></span>
                                            <span class="previous-value">vs. R$ <?= number_format($previousWeek['revenue'] ?? 0, 0, ',', '.') ?></span>
                                        </div>
                                        <div class="metric-label">Receita</div>
                                        <?php
                                        $revenueChange = 0;
                                        if (($previousWeek['revenue'] ?? 0) > 0) {
                                            $revenueChange = ((($currentWeek['revenue'] ?? 0) - ($previousWeek['revenue'] ?? 0)) / ($previousWeek['revenue'] ?? 1)) * 100;
                                        }
                                        ?>
                                        <div class="metric-change <?= $revenueChange >= 0 ? 'positive' : 'negative' ?>">
                                            <i class="bi bi-arrow-<?= $revenueChange >= 0 ? 'up' : 'down' ?>"></i>
                                            <?= abs(number_format($revenueChange, 1)) ?>%
                                        </div>
                                    </div>
                                </div>

                                <div class="comparison-metric">
                                    <div class="metric-icon">
                                        <i class="bi bi-receipt"></i>
                                    </div>
                                    <div class="metric-content">
                                        <div class="metric-values">
                                            <span class="current-value">R$ <?= number_format($currentWeek['avg_order_value'] ?? 0, 2, ',', '.') ?></span>
                                            <span class="previous-value">vs. R$ <?= number_format($previousWeek['avg_order_value'] ?? 0, 2, ',', '.') ?></span>
                                        </div>
                                        <div class="metric-label">Ticket Médio</div>
                                        <?php
                                        $avgChange = 0;
                                        if (($previousWeek['avg_order_value'] ?? 0) > 0) {
                                            $avgChange = ((($currentWeek['avg_order_value'] ?? 0) - ($previousWeek['avg_order_value'] ?? 0)) / ($previousWeek['avg_order_value'] ?? 1)) * 100;
                                        }
                                        ?>
                                        <div class="metric-change <?= $avgChange >= 0 ? 'positive' : 'negative' ?>">
                                            <i class="bi bi-arrow-<?= $avgChange >= 0 ? 'up' : 'down' ?>"></i>
                                            <?= abs(number_format($avgChange, 1)) ?>%
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Análise Diária e Performance de Pizzas -->
                <div class="analytics-row">
                    <div class="analytics-card daily-trends">
                        <div class="card-header">
                            <h3><i class="bi bi-graph-up"></i> Tendência Diária (30 dias)</h3>
                            <div class="chart-filters">
                                <span class="filter-label">Últimos 30 dias</span>
                            </div>
                        </div>
                        <div class="card-content">
                            <?php if (!empty($dailyAnalytics)): ?>
                                <div class="daily-chart">
                                    <?php
                                    $maxDailyRevenue = max(array_column($dailyAnalytics, 'revenue'));
                                    $dailyAnalytics = array_reverse($dailyAnalytics); // Mostrar do mais antigo para o mais recente
                                    foreach (array_slice($dailyAnalytics, -14) as $day): // Últimos 14 dias para melhor visualização
                                        $percentage = $maxDailyRevenue > 0 ? ($day['revenue'] / $maxDailyRevenue) * 100 : 0;
                                        $dayName = date('d/m', strtotime($day['date']));
                                    ?>
                                        <div class="daily-bar" title="<?= $dayName ?>: R$ <?= number_format($day['revenue'], 2, ',', '.') ?> - <?= $day['orders_count'] ?> pedidos">
                                            <div class="bar-fill" style="height: <?= max($percentage, 3) ?>%"></div>
                                            <div class="bar-label">
                                                <span class="day"><?= $dayName ?></span>
                                                <span class="revenue">R$ <?= number_format($day['revenue'], 0, ',', '.') ?></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="daily-insights">
                                    <?php
                                    $avgDailyRevenue = array_sum(array_column($dailyAnalytics, 'revenue')) / count($dailyAnalytics);
                                    $avgDailyOrders = array_sum(array_column($dailyAnalytics, 'orders_count')) / count($dailyAnalytics);
                                    $avgOrderValue = $avgDailyOrders > 0 ? $avgDailyRevenue / $avgDailyOrders : 0;
                                    ?>
                                    <div class="insight-grid">
                                        <div class="insight-item">
                                            <span class="insight-value">R$ <?= number_format($avgDailyRevenue, 2, ',', '.') ?></span>
                                            <span class="insight-label">Receita Média/Dia</span>
                                        </div>
                                        <div class="insight-item">
                                            <span class="insight-value"><?= number_format($avgDailyOrders, 1) ?></span>
                                            <span class="insight-label">Pedidos Média/Dia</span>
                                        </div>
                                        <div class="insight-item">
                                            <span class="insight-value">R$ <?= number_format($avgOrderValue, 2, ',', '.') ?></span>
                                            <span class="insight-label">Ticket Médio</span>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="bi bi-calendar-x"></i>
                                    <p>Nenhum dado diário disponível</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="analytics-card pizza-performance">
                        <div class="card-header">
                            <h3><i class="bi bi-pie-chart-fill"></i> Performance Detalhada das Pizzas</h3>
                            <a href="?page=pizzas" class="view-all">Gerenciar Cardápio</a>
                        </div>
                        <div class="card-content">
                            <?php if (!empty($pizzaPerformance)): ?>
                                <div class="performance-list">
                                    <?php foreach (array_slice($pizzaPerformance, 0, 8) as $index => $pizza): ?>
                                        <div class="performance-item">
                                            <div class="pizza-rank"><?= $index + 1 ?>º</div>
                                            <div class="pizza-details">
                                                <div class="pizza-name"><?= htmlspecialchars($pizza['name']) ?></div>
                                                <div class="pizza-metrics">
                                                    <span class="metric">
                                                        <i class="bi bi-cart"></i>
                                                        <?= $pizza['total_sold'] ?> vendas
                                                    </span>
                                                    <span class="metric">
                                                        <i class="bi bi-currency-dollar"></i>
                                                        R$ <?= number_format($pizza['total_revenue'], 0, ',', '.') ?>
                                                    </span>
                                                    <span class="metric">
                                                        <i class="bi bi-percent"></i>
                                                        <?= number_format($pizza['avg_quantity_per_order'], 1) ?> média/pedido
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="performance-bar">
                                                <?php
                                                $maxRevenue = $pizzaPerformance[0]['total_revenue'] ?? 1;
                                                $percentage = ($pizza['total_revenue'] / $maxRevenue) * 100;
                                                ?>
                                                <div class="bar-fill" style="width: <?= $percentage ?>%"></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="bi bi-pizza"></i>
                                    <p>Nenhuma pizza vendida ainda</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Análise de Clientes -->
                <div class="analytics-row">
                    <div class="analytics-card customer-insights">
                        <div class="card-header">
                            <h3><i class="bi bi-people-fill"></i> Insights de Clientes</h3>
                            <a href="?page=customers" class="view-all">Ver Clientes</a>
                        </div>
                        <div class="card-content">
                            <div class="customer-analytics-grid">
                                <div class="customer-chart">
                                    <h4>Novos Clientes por Mês</h4>
                                    <?php if (!empty($customerAnalytics['newCustomers'])): ?>
                                        <div class="customer-growth-chart">
                                            <?php
                                            $maxNewCustomers = max(array_column($customerAnalytics['newCustomers'], 'new_customers'));
                                            foreach ($customerAnalytics['newCustomers'] as $month):
                                                $percentage = $maxNewCustomers > 0 ? ($month['new_customers'] / $maxNewCustomers) * 100 : 0;
                                                $monthName = date('M/y', strtotime($month['month'] . '-01'));
                                            ?>
                                                <div class="growth-bar">
                                                    <div class="bar-fill" style="height: <?= max($percentage, 10) ?>%" title="<?= $monthName ?>: <?= $month['new_customers'] ?> novos clientes"></div>
                                                    <div class="bar-label">
                                                        <span class="month"><?= $monthName ?></span>
                                                        <span class="count"><?= $month['new_customers'] ?></span>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="empty-state-small">
                                            <i class="bi bi-person-plus"></i>
                                            <p>Nenhum dado disponível</p>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="top-customers">
                                    <h4>Clientes VIP</h4>
                                    <?php if (!empty($customerAnalytics['recurringCustomers'])): ?>
                                        <div class="vip-customers-list">
                                            <?php foreach (array_slice($customerAnalytics['recurringCustomers'], 0, 5) as $index => $customer): ?>
                                                <div class="vip-customer-item">
                                                    <div class="customer-rank"><?= $index + 1 ?>º</div>
                                                    <div class="customer-info">
                                                        <div class="customer-name"><?= htmlspecialchars($customer['name']) ?></div>
                                                        <div class="customer-stats">
                                                            <span class="stat">
                                                                <i class="bi bi-bag-check"></i>
                                                                <?= $customer['total_orders'] ?> pedidos
                                                            </span>
                                                            <span class="stat">
                                                                <i class="bi bi-currency-dollar"></i>
                                                                R$ <?= number_format($customer['total_spent'], 2, ',', '.') ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="last-order">
                                                        <?= date('d/m/Y', strtotime($customer['last_order'])) ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="empty-state-small">
                                            <i class="bi bi-star"></i>
                                            <p>Nenhum cliente VIP ainda</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dashboard-alerts">
                <div class="alert-card info">
                    <i class="bi bi-info-circle"></i>
                    <div>
                        <h4>Sistema Operacional</h4>
                        <p>Todos os serviços estão funcionando normalmente. Última atualização: <?= date('d/m/Y H:i') ?></p>
                    </div>
                </div>
            </div>
        </main>
<?php
    }
}
