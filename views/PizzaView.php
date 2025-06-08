<?php

namespace App\Views;

use App\Models\Pizza;

abstract class PizzaView
{
    public static function renderForm(?string $message = null, ?Pizza $pizza = null, ?array $fieldsWithErrors = null, ?array $formData = null): void
    {
        $isEdit = $pizza !== null;
        $title = $isEdit ? "Editar Pizza" : "Nova Pizza";
        $action = $isEdit ? "?page=pizzas&action=edit" : "?page=pizzas&action=create";

        $nameValue = $formData['name'] ?? ($pizza ? htmlspecialchars($pizza->name) : '');
        $descriptionValue = $formData['description'] ?? ($pizza ? htmlspecialchars($pizza->description) : '');
        $priceValue = $formData['price'] ?? ($pizza ? number_format($pizza->price, 2) : '');
        $categoryValue = $formData['category'] ?? ($pizza ? htmlspecialchars($pizza->category) : '');

        try {
            $categories = \App\Dal\PizzaDao::getCategories();
        } catch (\Exception $e) {
            $categories = ['Tradicionais', 'Especiais', 'Doces'];
        }
?>
        <main>
            <h1><?= $title ?></h1>

            <div class="navigation-buttons">
                <a href="?page=pizzas"><i class="bi bi-arrow-left"></i> Voltar</a>
            </div>

            <section>
                <?php if ($message): ?>
                    <p class="message error">
                        <?= htmlspecialchars($message) ?>
                    </p>
                <?php endif; ?>

                <form method="POST" action="<?= $action ?>">
                    <?php if ($isEdit): ?>
                        <input type="hidden" name="id" value="<?= $pizza->id ?>">
                    <?php endif; ?>

                    <label for="name">Nome da Pizza:</label>
                    <input type="text" id="name" name="name"
                        value="<?= $nameValue ?>"
                        placeholder="Digite o nome da pizza"
                        class="<?= $fieldsWithErrors && in_array('name', $fieldsWithErrors) ? 'field-error' : '' ?>">

                    <label for="description">Descrição:</label>
                    <textarea id="description" name="description"
                        placeholder="Digite a descrição da pizza"
                        rows="4"
                        class="<?= $fieldsWithErrors && in_array('description', $fieldsWithErrors) ? 'field-error' : '' ?>"><?= $descriptionValue ?></textarea>

                    <label for="price">Preço (R$):</label>
                    <input type="number" id="price" name="price" step="0.01" min="0"
                        value="<?= $priceValue ?>"
                        placeholder="Digite o preço"
                        class="<?= $fieldsWithErrors && in_array('price', $fieldsWithErrors) ? 'field-error' : '' ?>">

                    <label for="category">Categoria:</label>
                    <select id="category" name="category"
                        class="<?= $fieldsWithErrors && in_array('category', $fieldsWithErrors) ? 'field-error' : '' ?>">
                        <option value="">Selecione uma categoria</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars($category) ?>" <?= $categoryValue === $category ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category) ?>
                            </option>
                        <?php endforeach; ?>
                        <option value="Nova Categoria" <?= !in_array($categoryValue, $categories) && !empty($categoryValue) ? 'selected' : '' ?>>
                            Nova Categoria
                        </option>
                    </select>

                    <div id="new-category-field" style="display: none;">
                        <label for="new_category">Nome da Nova Categoria:</label>
                        <input type="text" id="new_category" name="new_category"
                            placeholder="Digite o nome da nova categoria"
                            value="<?= !in_array($categoryValue, $categories) && !empty($categoryValue) ? htmlspecialchars($categoryValue) : '' ?>">
                    </div>

                    <button type="submit"><?= $isEdit ? 'Atualizar' : 'Criar' ?> Pizza</button>
                </form>
            </section>
        </main>
    <?php
    }

    public static function renderList(array $pizzas, ?int $deleteId = null): void
    {
    ?>
        <main>
            <h1>Gerenciar Pizzas</h1>

            <div class="navigation-buttons">
                <a href="?page=pizzas&action=create"><i class="bi bi-plus-lg"></i> Nova Pizza</a>
            </div>

            <?php if ($deleteId): ?>
                <section class="delete-confirmation">
                    <p>Tem certeza que deseja excluir esta pizza?</p>
                    <nav>
                        <a href="?page=pizzas&action=delete&id=<?= $deleteId ?>" class="btn-danger"><i class="bi bi-trash3"></i> Confirmar Exclusão</a>
                        <a href="?page=pizzas"><i class="bi bi-arrow-left"></i> Cancelar</a>
                    </nav>
                </section>
            <?php endif; ?>

            <?php if (!empty($pizzas)): ?>
                <div class="pizza-grid">
                    <?php foreach ($pizzas as $pizza): ?>
                        <div class="pizza-card">
                            <div class="pizza-card-header">
                                <div class="pizza-id">ID: <?= $pizza->id ?></div>
                                <div class="pizza-category"><?= htmlspecialchars($pizza->category) ?></div>
                            </div>

                            <div class="pizza-card-body">
                                <h3 class="pizza-title"><?= htmlspecialchars($pizza->name) ?></h3>
                                <p class="pizza-description"><?= htmlspecialchars($pizza->description) ?></p>
                                <div class="pizza-price">R$ <?= number_format($pizza->price, 2, ',', '.') ?></div>
                            </div>

                            <div class="pizza-card-actions">
                                <a href="?page=pizzas&action=edit&edit=<?= $pizza->id ?>" class="btn-edit">
                                    <i class="bi bi-pencil-square"></i> Editar
                                </a>
                                <a href="?page=pizzas&confirm=<?= $pizza->id ?>" class="btn-delete">
                                    <i class="bi bi-trash3"></i> Excluir
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <p>Nenhuma pizza encontrada.</p>
                </div>
            <?php endif; ?>
        </main>
    <?php
    }

    public static function renderPublicMenu(array $pizzasByCategory): void
    {
    ?>
        <main class="container">
            <section class="menu-section">
                <h1 class="menu-title">Cardápio de Pizzas</h1>

                <?php foreach ($pizzasByCategory as $category => $pizzas): ?>
                    <div class="menu-category">
                        <h2><?= htmlspecialchars($category) ?></h2>
                        <div class="pizza-list">
                            <?php foreach ($pizzas as $pizza): ?>
                                <div class="pizza-item">
                                    <div class="pizza-info">
                                        <h3><?= htmlspecialchars($pizza->name) ?></h3>
                                        <p><?= htmlspecialchars($pizza->description) ?></p>
                                    </div>
                                    <div class="pizza-price">
                                        R$ <?= number_format($pizza->price, 2, ',', '.') ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($pizzasByCategory)): ?>
                    <div class="empty-state">
                        <p>Nenhuma pizza disponível no momento.</p>
                    </div>
                <?php endif; ?>
            </section>
        </main>
<?php
    }
}
