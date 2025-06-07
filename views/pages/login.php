<?php

use App\Controllers\UserController;

session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: ?page=dashboard");
    exit;
}

// Processa o login
UserController::login();
?>

<main class="login">
    <div class="login-container">
        <div class="logo-section">
            <img src="./assets/images/logo.png" alt="Pizza Universe Logo" height="80">
            <h1>Pizza Universe</h1>
            <p>Painel Administrativo</p>
        </div>

        <?php if (UserController::$msg): ?>
            <div class="error-message">
                <?= htmlspecialchars(UserController::$msg) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="?page=login">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required placeholder="Digite seu email">
            </div>

            <div class="form-group">
                <label for="password">Senha:</label>
                <input type="password" id="password" name="password" required placeholder="Digite sua senha">
            </div>

            <button type="submit">Entrar</button>
        </form>

        <div class="login-footer">
            <p>Acesso restrito aos administradores</p>
            <a href="./index.php">← Voltar ao site</a>
        </div>
    </div>
</main>