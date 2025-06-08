<?php

namespace App\Views;

use App\Models\User;

abstract class UserView
{
    public static function renderLogin(?string $message = null): void
    {
?>
        <div class="login-page">
            <div class="login-container">
                <img src="./assets/images/logo.png" alt="Pizza Universe Logo" height="80">
                <h1>Pizza Universe</h1>
                <p>Painel Administrativo</p>

                <?php if ($message): ?>
                    <div class="message error">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="?page=login" class="auth-form">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required placeholder="Digite seu email">
                    </div>

                    <div class="form-group">
                        <label for="password">Senha:</label>
                        <input type="password" id="password" name="password" required placeholder="Digite sua senha">
                    </div>

                    <button type="submit" class="auth-button">Entrar</button>
                </form>
            </div>
        </div>
    <?php
    }

    public static function renderForm(?string $message = null, ?User $user = null): void
    {
        $isEdit = $user !== null;
        $title = $isEdit ? "Editar Usuário" : "Novo Usuário";
        $action = $isEdit ? "?page=users&action=edit" : "?page=users&action=create";
    ?>
        <main>
            <h1><?= $title ?></h1>
            <a href="?page=users">← Voltar</a>

            <div>
                <?php if ($message): ?>
                    <div>
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= $action ?>">
                    <?php if ($isEdit): ?>
                        <input type="hidden" name="id" value="<?= $user->id ?>">
                    <?php endif; ?>

                    <div>
                        <label for="username">Nome de usuário:</label>
                        <input type="text" id="username" name="username" required
                            value="<?= $user ? htmlspecialchars($user->username) : '' ?>"
                            placeholder="Digite o nome de usuário">
                    </div>

                    <div>
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required
                            value="<?= $user ? htmlspecialchars($user->email) : '' ?>"
                            placeholder="Digite o email">
                    </div>

                    <div>
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
        <main>
            <h1>Gerenciar Usuários</h1>
            <a href="?page=users&action=create">+ Novo Usuário</a>

            <?php if ($deleteId): ?>
                <div>
                    <p>Tem certeza que deseja excluir este usuário?</p>
                    <div>
                        <a href="?page=users&action=delete&id=<?= $deleteId ?>">Confirmar Exclusão</a>
                        <a href="?page=users">Cancelar</a>
                    </div>
                </div>
            <?php endif; ?>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user->id ?></td>
                            <td><?= htmlspecialchars($user->username) ?></td>
                            <td><?= htmlspecialchars($user->email) ?></td>
                            <td>
                                <a href="?page=users&action=edit&edit=<?= $user->id ?>">Editar</a>
                                <a href="?page=users&confirm=<?= $user->id ?>">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if (empty($users)): ?>
                <p>Nenhum usuário encontrado.</p>
            <?php endif; ?>
        </main>
<?php
    }
}
