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

        // Carregar categorias dinamicamente do banco
        try {
            $categories = \App\Dal\PizzaDao::getCategories();
        } catch (\Exception $e) {
            $categories = ['Tradicionais', 'Especiais', 'Doces']; // fallback
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

                    <!-- Campo para digitar nova categoria -->
                    <div id="new-category-field" style="display: none; margin-top: 10px;">
                        <label for="new_category">Nome da Nova Categoria:</label>
                        <input type="text" id="new_category" name="new_category"
                            placeholder="Digite o nome da nova categoria"
                            value="<?= !in_array($categoryValue, $categories) && !empty($categoryValue) ? htmlspecialchars($categoryValue) : '' ?>">
                    </div>

                    <button type="submit"><?= $isEdit ? 'Atualizar' : 'Criar' ?> Pizza</button>
                </form>
            </section>
        </main>

        <script>
            document.getElementById('category').addEventListener('change', function() {
                const newCategoryField = document.getElementById('new-category-field');
                const newCategoryInput = document.getElementById('new_category');

                if (this.value === 'Nova Categoria') {
                    newCategoryField.style.display = 'block';
                    newCategoryInput.required = true;
                } else {
                    newCategoryField.style.display = 'none';
                    newCategoryInput.required = false;
                    newCategoryInput.value = '';
                }
            });

            // Mostrar campo se já existe uma categoria personalizada
            window.addEventListener('load', function() {
                const categorySelect = document.getElementById('category');
                const selectedValue = categorySelect.value;
                const newCategoryField = document.getElementById('new-category-field');

                if (selectedValue && categorySelect.querySelector(`option[value="${selectedValue}"]`) === null) {
                    // Se a categoria atual não está nas opções padrão, mostrar campo
                    categorySelect.value = 'Nova Categoria';
                    newCategoryField.style.display = 'block';
                    document.getElementById('new_category').required = true;
                }
            });
        </script>

        <style>
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }

            th,
            td {
                padding: 12px;
                text-align: left;
                border-bottom: 1px solid #ddd;
            }

            th {
                background-color: #f2f2f2;
                font-weight: bold;
            }

            tr:hover {
                background-color: #f5f5f5;
            }

            .navigation-buttons {
                margin: 20px 0;
            }

            .navigation-buttons a {
                background-color: #007bff;
                color: white;
                padding: 10px 15px;
                text-decoration: none;
                border-radius: 5px;
                margin-right: 10px;
            }

            .navigation-buttons a:hover {
                background-color: #0056b3;
            }

            .field-error {
                border-color: #dc3545;
                background-color: #f8d7da;
            }

            .message.error {
                color: #721c24;
                background-color: #f8d7da;
                border: 1px solid #f5c6cb;
                padding: 10px;
                border-radius: 5px;
                margin: 10px 0;
            }

            form {
                max-width: 600px;
            }

            label {
                display: block;
                margin-top: 15px;
                margin-bottom: 5px;
                font-weight: bold;
            }

            input,
            textarea,
            select {
                width: 100%;
                padding: 10px;
                border: 1px solid #ddd;
                border-radius: 5px;
                font-size: 16px;
            }

            button {
                background-color: #28a745;
                color: white;
                padding: 12px 20px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 16px;
                margin-top: 20px;
            }

            button:hover {
                background-color: #218838;
            }

            .pizza-list {
                display: grid;
                gap: 15px;
                margin-bottom: 30px;
            }

            .pizza-item {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                padding: 15px;
                border: 1px solid #eee;
                border-radius: 8px;
                background-color: #fff;
            }

            .pizza-info h3 {
                margin: 0 0 8px 0;
                color: #333;
                font-size: 1.1em;
            }

            .pizza-info p {
                margin: 0;
                color: #666;
                font-size: 0.9em;
                line-height: 1.4;
            }

            .pizza-price {
                font-weight: bold;
                color: #e74c3c;
                font-size: 1.1em;
                white-space: nowrap;
                margin-left: 15px;
            }
        </style>
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
                <section>
                    <p>Tem certeza que deseja excluir esta pizza?</p>
                    <nav>
                        <a href="?page=pizzas&action=delete&id=<?= $deleteId ?>"><i class="bi bi-trash3"></i> Confirmar Exclusão</a>
                        <a href="?page=pizzas"><i class="bi bi-arrow-left"></i> Cancelar</a>
                    </nav>
                </section>
            <?php endif; ?>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Preço</th>
                        <th>Categoria</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pizzas as $pizza): ?>
                        <tr>
                            <td><?= $pizza->id ?></td>
                            <td><?= htmlspecialchars($pizza->name) ?></td>
                            <td><?= htmlspecialchars(substr($pizza->description, 0, 50)) . (strlen($pizza->description) > 50 ? '...' : '') ?></td>
                            <td>R$ <?= number_format($pizza->price, 2, ',', '.') ?></td>
                            <td><?= htmlspecialchars($pizza->category) ?></td>
                            <td>
                                <a href="?page=pizzas&action=edit&edit=<?= $pizza->id ?>"><i class="bi bi-pencil-square"></i> Editar</a>
                                <a href="?page=pizzas&confirm=<?= $pizza->id ?>"><i class="bi bi-trash3"></i> Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if (empty($pizzas)): ?>
                <p>Nenhuma pizza encontrada.</p>
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
                    <p>Nenhuma pizza disponível no momento.</p>
                <?php endif; ?>
            </section>
        </main>

        <style>
            .pizza-list {
                display: grid;
                gap: 15px;
                margin-bottom: 30px;
            }

            .pizza-item {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                padding: 15px;
                border: 1px solid #eee;
                border-radius: 8px;
                background-color: #fff;
            }

            .pizza-info h3 {
                margin: 0 0 8px 0;
                color: #333;
                font-size: 1.1em;
            }

            .pizza-info p {
                margin: 0;
                color: #666;
                font-size: 0.9em;
                line-height: 1.4;
            }

            .pizza-price {
                font-weight: bold;
                color: #e74c3c;
                font-size: 1.1em;
                white-space: nowrap;
                margin-left: 15px;
            }
        </style>
<?php
    }
}
