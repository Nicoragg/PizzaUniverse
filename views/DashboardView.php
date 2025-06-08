<?php

namespace App\Views;

abstract class DashboardView
{
    public static function render(array $stats, array $recentOrders, array $topPizzas, array $monthlyRevenue, array $ordersByStatus): void
    {
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

                <div class="dashboard-card monthly-revenue">
                    <div class="card-header">
                        <h3><i class="bi bi-graph-up"></i> Receita dos Últimos Meses</h3>
                        <div class="revenue-summary">
                            <?php if (!empty($monthlyRevenue)): ?>
                                <span class="total-months"><?= count($monthlyRevenue) ?> meses</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-content">
                        <?php if (!empty($monthlyRevenue)): ?>
                            <div class="revenue-stats-header">
                                <?php
                                $totalRevenue = array_sum(array_column($monthlyRevenue, 'revenue'));
                                $totalOrders = array_sum(array_column($monthlyRevenue, 'orders_count'));
                                $avgRevenue = count($monthlyRevenue) > 0 ? $totalRevenue / count($monthlyRevenue) : 0;
                                ?>
                                <div class="revenue-metric">
                                    <span class="metric-value">R$ <?= number_format($totalRevenue, 2, ',', '.') ?></span>
                                    <span class="metric-label">Total do Período</span>
                                </div>
                                <div class="revenue-metric">
                                    <span class="metric-value"><?= $totalOrders ?></span>
                                    <span class="metric-label">Pedidos Totais</span>
                                </div>
                                <div class="revenue-metric">
                                    <span class="metric-value">R$ <?= number_format($avgRevenue, 2, ',', '.') ?></span>
                                    <span class="metric-label">Média Mensal</span>
                                </div>
                            </div>

                            <div class="revenue-chart">
                                <?php
                                $maxRevenue = max(array_column($monthlyRevenue, 'revenue'));
                                foreach ($monthlyRevenue as $index => $month):
                                    $percentage = $maxRevenue > 0 ? ($month['revenue'] / $maxRevenue) * 100 : 0;
                                    $monthName = [
                                        '01' => 'Jan',
                                        '02' => 'Fev',
                                        '03' => 'Mar',
                                        '04' => 'Abr',
                                        '05' => 'Mai',
                                        '06' => 'Jun',
                                        '07' => 'Jul',
                                        '08' => 'Ago',
                                        '09' => 'Set',
                                        '10' => 'Out',
                                        '11' => 'Nov',
                                        '12' => 'Dez'
                                    ][date('m', strtotime($month['month'] . '-01'))];
                                    $year = date('Y', strtotime($month['month'] . '-01'));
                                ?>
                                    <div class="revenue-bar" data-tooltip="<?= $monthName ?>/<?= $year ?>: R$ <?= number_format($month['revenue'], 2, ',', '.') ?> - <?= $month['orders_count'] ?> pedidos">
                                        <div class="bar-fill" style="height: <?= max($percentage, 5) ?>%" data-value="<?= $month['revenue'] ?>"></div>
                                        <div class="bar-label">
                                            <span class="month"><?= $monthName ?>/<?= substr($year, 2) ?></span>
                                            <span class="amount">R$ <?= number_format($month['revenue'], 0, ',', '.') ?></span>
                                            <span class="orders"><?= $month['orders_count'] ?> pedidos</span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="revenue-insights">
                                <?php
                                $lastMonth = end($monthlyRevenue);
                                $previousMonth = count($monthlyRevenue) > 1 ? $monthlyRevenue[count($monthlyRevenue) - 2] : null;
                                $growth = 0;
                                if ($previousMonth && $previousMonth['revenue'] > 0) {
                                    $growth = (($lastMonth['revenue'] - $previousMonth['revenue']) / $previousMonth['revenue']) * 100;
                                }
                                $bestMonth = array_reduce($monthlyRevenue, function ($carry, $month) {
                                    return (!$carry || $month['revenue'] > $carry['revenue']) ? $month : $carry;
                                });
                                ?>
                                <div class="insight-item">
                                    <i class="bi bi-trending-<?= $growth >= 0 ? 'up' : 'down' ?>"></i>
                                    <div class="insight-content">
                                        <span class="insight-value <?= $growth >= 0 ? 'positive' : 'negative' ?>">
                                            <?= $growth >= 0 ? '+' : '' ?><?= number_format($growth, 1) ?>%
                                        </span>
                                        <span class="insight-label">vs. mês anterior</span>
                                    </div>
                                </div>
                                <?php if ($bestMonth): ?>
                                    <div class="insight-item">
                                        <i class="bi bi-star-fill"></i>
                                        <div class="insight-content">
                                            <span class="insight-value best-month">
                                                <?= date('M/Y', strtotime($bestMonth['month'] . '-01')) ?>
                                            </span>
                                            <span class="insight-label">melhor mês</span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="bi bi-graph-up"></i>
                                <p>Nenhum dado de receita disponível</p>
                                <small>Comece fazendo alguns pedidos para ver os gráficos aqui!</small>
                            </div>
                        <?php endif; ?>
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
