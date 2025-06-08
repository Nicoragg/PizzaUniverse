<?php

namespace App\Views;

use App\Models\User;

abstract class UserView
{
    public static function renderLogin(?string $message = null, ?array $fieldsWithErrors = null, ?array $formData = null): void
    {
?>
        <section class="login-page">
            <section class="login-container">
                <img src="./assets/images/logo.png" alt="Pizza Universe Logo" height="80">
                <h1>Pizza Universe</h1>
                <p>Painel Administrativo</p>

                <?php if ($message): ?>
                    <p class="message error">
                        <?= htmlspecialchars($message) ?>
                    </p>
                <?php endif; ?>

                <form method="POST" action="?page=login" class="auth-form">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email"
                            value="<?= $formData['email'] ?? '' ?>"
                            placeholder="Digite seu email">
                    </div>

                    <div class="form-group">
                        <label for="password">Senha:</label>
                        <input type="password" id="password" name="password"
                            value="<?= $formData['password'] ?? '' ?>"
                            placeholder="Digite sua senha">
                    </div>

                    <button type="submit" class="auth-button">Entrar</button>
                </form>
            </section>
        </section>
    <?php
    }

    public static function renderForm(?string $message = null, ?User $user = null, ?array $fieldsWithErrors = null, ?array $formData = null): void
    {
        $isEdit = $user !== null;
        $title = $isEdit ? "Editar Usuário" : "Novo Usuário";
        $action = $isEdit ? "?page=users&action=edit" : "?page=users&action=create";

        $usernameValue = $formData['username'] ?? ($user ? htmlspecialchars($user->username) : '');
        $emailValue = $formData['email'] ?? ($user ? htmlspecialchars($user->email) : '');
        $passwordValue = $formData['password'] ?? '';
    ?>
        <main>
            <h1><?= $title ?></h1>

            <div class="navigation-buttons">
                <a href="?page=users"><i class="bi bi-arrow-left"></i> Voltar</a>
            </div>

            <section>
                <?php if ($message): ?>
                    <p class="message error">
                        <?= htmlspecialchars($message) ?>
                    </p>
                <?php endif; ?>

                <form method="POST" action="<?= $action ?>">
                    <?php if ($isEdit): ?>
                        <input type="hidden" name="id" value="<?= $user->id ?>">
                    <?php endif; ?>

                    <label for="username">Nome de usuário:</label>
                    <input type="text" id="username" name="username"
                        value="<?= $usernameValue ?>"
                        placeholder="Digite o nome de usuário"
                        class="<?= $fieldsWithErrors && in_array('username', $fieldsWithErrors) ? 'field-error' : '' ?>">

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email"
                        value="<?= $emailValue ?>"
                        placeholder="Digite o email"
                        class="<?= $fieldsWithErrors && in_array('email', $fieldsWithErrors) ? 'field-error' : '' ?>">

                    <label for="password">Senha:</label>
                    <input type="password" id="password" name="password"
                        value="<?= $passwordValue ?>"
                        placeholder="<?= $isEdit ? 'Deixe em branco para manter a senha atual' : 'Digite a senha' ?>"
                        class="<?= $fieldsWithErrors && in_array('password', $fieldsWithErrors) ? 'field-error' : '' ?>">

                    <button type="submit"><?= $isEdit ? 'Atualizar' : 'Criar' ?> Usuário</button>
                </form>
            </section>
        </main>
    <?php
    }

    public static function renderList(array $users, ?int $deleteId = null): void
    {
    ?>
        <main>
            <h1>Gerenciar Usuários</h1>

            <div class="navigation-buttons">
                <a href="?page=users&action=create"><i class="bi bi-plus-lg"></i> Novo Usuário</a>
            </div>

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
                                <a href="?page=users&action=edit&edit=<?= $user->id ?>"><i class="bi bi-pencil-square"></i> Editar</a>
                                <a href="?page=users&confirm=<?= $user->id ?>"><i class="bi bi-trash3"></i> Excluir</a>
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
