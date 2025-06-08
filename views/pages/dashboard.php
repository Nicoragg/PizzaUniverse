<?php

use App\Controllers\UserController;

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    UserController::logout();
}

$username = $_SESSION['username'] ?? 'Administrador';
$email = $_SESSION['email'] ?? '';
?>

<main class="dashboard">
    <div class="dashboard-header">
        <h1>Painel Administrativo</h1>
        <div class="user-info">
            <span>Bem-vindo, <?= htmlspecialchars($username) ?>!</span>
            <a href="?page=dashboard&action=logout" class="logout-btn">Sair</a>
        </div>
    </div>

    <div class="dashboard-content">
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h3>👥 Usuários</h3>
                <p>Gerenciar usuários do sistema</p>
                <a href="?page=users" class="btn-primary">Acessar</a>
            </div>

            <div class="dashboard-card">
                <h3>👤 Clientes</h3>
                <p>Gerenciar clientes e dados pessoais</p>
                <a href="?page=customers" class="btn-primary">Acessar</a>
            </div>

            <div class="dashboard-card">
                <h3>🍕 Pizzas</h3>
                <p>Gerenciar pizzas e categorias</p>
                <a href="?page=pizzas" class="btn-primary">Acessar</a>
            </div>

            <div class="dashboard-card">
                <h3>📋 Cardápio</h3>
                <p>Visualizar cardápio público</p>
                <a href="?page=menu" class="btn-primary">Acessar</a>
            </div>

            <div class="dashboard-card">
                <h3>🚚 Pedidos</h3>
                <p>Criar e gerenciar pedidos</p>
                <a href="#" class="btn-primary">Acessar</a>
            </div>

            <div class="dashboard-card">
                <h3>📊 Relatórios</h3>
                <p>Estatísticas e relatórios</p>
                <a href="?page=reports" class="btn-primary">Acessar</a>
            </div>

            <div class="dashboard-card">
                <h3>⚙️ Configurações</h3>
                <p>Configurações do sistema</p>
                <a href="?page=settings" class="btn-primary">Acessar</a>
            </div>
        </div>

        <div class="dashboard-stats">
            <h2>Resumo do Sistema</h2>
            <div class="stats-grid">
                <div class="stat-item">
                    <h4>Total de Usuários</h4>
                    <span class="stat-number">--</span>
                </div>
                <div class="stat-item">
                    <h4>Pedidos Hoje</h4>
                    <span class="stat-number">--</span>
                </div>
                <div class="stat-item">
                    <h4>Pedidos Pendentes</h4>
                    <span class="stat-number">--</span>
                </div>
            </div>
        </div>
    </div>
</main>