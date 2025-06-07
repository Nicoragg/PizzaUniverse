<?php

namespace App\Views;

use App\Models\User;

abstract class UserView
{
    public static function renderLogin(?string $message = null): void
    {
?>
        <main class="login">
            <div class="login-container">
                <div class="logo-section">
                    <img src="./assets/images/logo.png" alt="Pizza Universe Logo" height="80">
                    <h1>Pizza Universe</h1>
                    <p>Painel Administrativo</p>
                </div>

                <?php if ($message): ?>
                    <div class="error-message">
                        <?= htmlspecialchars($message) ?>
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
    <?php
    }

    public static function renderForm(?string $message = null, ?User $user = null): void
    {
        $isEdit = $user !== null;
        $title = $isEdit ? "Editar Usuário" : "Novo Usuário";
        $action = $isEdit ? "?page=users&action=edit" : "?page=users&action=create";
    ?>
        <main class="dashboard">
            <div class="dashboard-header">
                <h1><?= $title ?></h1>
                <a href="?page=users" class="btn-secondary">← Voltar</a>
            </div>

            <div class="login-container" style="max-width: 600px; margin: 0 auto;">
                <?php if ($message): ?>
                    <div class="error-message">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= $action ?>">
                    <?php if ($isEdit): ?>
                        <input type="hidden" name="id" value="<?= $user->id ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="username">Nome de usuário:</label>
                        <input type="text" id="username" name="username" required
                            value="<?= $user ? htmlspecialchars($user->username) : '' ?>"
                            placeholder="Digite o nome de usuário">
                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required
                            value="<?= $user ? htmlspecialchars($user->email) : '' ?>"
                            placeholder="Digite o email">
                    </div>

                    <div class="form-group">
                        <label for="password">Senha:</label>
                        <input type="password" id="password" name="password" required
                            placeholder="<?= $isEdit ? 'Nova senha' : 'Digite a senha' ?>">
                    </div>

                    <button type="submit"><?= $isEdit ? 'Atualizar' : 'Criar' ?> Usuário</button>
                </form>
            </div>
        </main>
    <?php
    }

    public static function renderList(array $users, ?int $deleteId = null): void
    {
    ?>
        <main class="dashboard">
            <div class="dashboard-header">
                <h1>Gerenciar Usuários</h1>
                <a href="?page=users&action=create" class="btn-primary">+ Novo Usuário</a>
            </div>

            <?php if ($deleteId): ?>
                <div class="delete-confirmation" style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
                    <p>Tem certeza que deseja excluir este usuário?</p>
                    <div style="margin-top: 1rem;">
                        <a href="?page=users&delete=<?= $deleteId ?>" class="btn-danger" style="background: #dc3545; color: white; padding: 8px 16px; border-radius: 5px; text-decoration: none; margin-right: 1rem;">Confirmar Exclusão</a>
                        <a href="?page=users" class="btn-secondary">Cancelar</a>
                    </div>
                </div>
            <?php endif; ?>

            <div class="dashboard-content">
                <div class="dashboard-card" style="text-align: left;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid #eee;">
                                <th style="padding: 1rem; text-align: left;">ID</th>
                                <th style="padding: 1rem; text-align: left;">Nome</th>
                                <th style="padding: 1rem; text-align: left;">Email</th>
                                <th style="padding: 1rem; text-align: left;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 1rem;"><?= $user->id ?></td>
                                    <td style="padding: 1rem;"><?= htmlspecialchars($user->username) ?></td>
                                    <td style="padding: 1rem;"><?= htmlspecialchars($user->email) ?></td>
                                    <td style="padding: 1rem;">
                                        <a href="?page=users&action=edit&edit=<?= $user->id ?>" class="btn-primary" style="padding: 5px 10px; font-size: 0.8rem; margin-right: 0.5rem;">Editar</a>
                                        <a href="?page=users&del=<?= $user->id ?>" class="btn-danger" style="background: #dc3545; color: white; padding: 5px 10px; border-radius: 5px; text-decoration: none; font-size: 0.8rem;">Excluir</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <?php if (empty($users)): ?>
                        <p style="text-align: center; color: #666; margin: 2rem 0;">Nenhum usuário encontrado.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
<?php
    }
}
