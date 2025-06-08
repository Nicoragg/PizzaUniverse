<?php

namespace App\Controllers;

use App\Models\Pizza;
use App\Dal\PizzaDao;
use App\Views\PizzaView;
use App\Util\Validator;
use App\Util\CsrfToken;
use function App\Util\validateInput;

abstract class PizzaController
{
    public static ?string $msg = null;
    public static ?array $fieldsWithErrors = null;
    public static ?array $formData = null;

    public static function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            // Validar token CSRF
            $token = validateInput($_POST[CsrfToken::getTokenName()] ?? '');
            if (!CsrfToken::validate($token)) {
                self::$msg = "Token de segurança inválido. Por favor, tente novamente.";
                PizzaView::renderForm(self::$msg, null, self::$fieldsWithErrors, self::$formData);
                return;
            }

            $name = validateInput($_POST["name"] ?? '');
            $description = validateInput($_POST["description"] ?? '');
            $price = validateInput($_POST["price"] ?? '');
            $category = validateInput($_POST["category"] ?? '');
            $newCategory = validateInput($_POST["new_category"] ?? '');

            if ($category === 'Nova Categoria' && !empty($newCategory)) {
                $category = $newCategory;
            }

            $validator = new Validator();
            $validator
                ->validateRequired('name', $name, 'Nome da Pizza')
                ->validateMinLength('name', $name, 3, 'Nome da Pizza')
                ->validateMaxLength('name', $name, 100, 'Nome da Pizza')
                ->validateRequired('description', $description, 'Descrição')
                ->validateMinLength('description', $description, 10, 'Descrição')
                ->validateMaxLength('description', $description, 500, 'Descrição')
                ->validateRequired('price', $price, 'Preço')
                ->validateRequired('category', $category, 'Categoria')
                ->validateMaxLength('category', $category, 50, 'Categoria');

            if (!empty($price) && !is_numeric($price)) {
                $validator->addError('price', 'O campo Preço deve ser um número válido.');
            } elseif (!empty($price) && (float) $price <= 0) {
                $validator->addError('price', 'O campo Preço deve ser maior que zero.');
            }

            if ($validator->hasErrors()) {
                self::$msg = $validator->getErrorsAsString();
                self::$fieldsWithErrors = $validator->getFieldsWithErrors();
                self::$formData = [
                    'name' => $validator->hasFieldError('name') ? '' : $name,
                    'description' => $validator->hasFieldError('description') ? '' : $description,
                    'price' => $validator->hasFieldError('price') ? '' : $price,
                    'category' => $validator->hasFieldError('category') ? '' : $category
                ];
            } else {
                try {
                    $pizza = new Pizza(0, $name, $description, (float) $price, $category);
                    $id = PizzaDao::create($pizza);
                    // Regenerar token após sucesso
                    CsrfToken::regenerate();
                    header("Location: ?page=pizzas");
                    exit;
                } catch (\Exception $e) {
                    self::$msg = $e->getMessage();
                }
            }
        }
        PizzaView::renderForm(self::$msg, null, self::$fieldsWithErrors, self::$formData);
    }

    public static function update(): void
    {
        $pizza = null;
        if (isset($_GET["edit"])) {
            $pizza = PizzaDao::findById((int) $_GET["edit"]);
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // Validar token CSRF
            $token = validateInput($_POST[CsrfToken::getTokenName()] ?? '');
            if (!CsrfToken::validate($token)) {
                self::$msg = "Token de segurança inválido. Por favor, tente novamente.";
                PizzaView::renderForm(self::$msg, $pizza, self::$fieldsWithErrors, self::$formData);
                return;
            }

            $id = (int) validateInput($_POST["id"] ?? '0');
            $name = validateInput($_POST["name"] ?? '');
            $description = validateInput($_POST["description"] ?? '');
            $price = validateInput($_POST["price"] ?? '');
            $category = validateInput($_POST["category"] ?? '');
            $newCategory = validateInput($_POST["new_category"] ?? '');

            if ($category === 'Nova Categoria' && !empty($newCategory)) {
                $category = $newCategory;
            }

            $validator = new Validator();
            $validator
                ->validateRequired('name', $name, 'Nome da Pizza')
                ->validateMinLength('name', $name, 3, 'Nome da Pizza')
                ->validateMaxLength('name', $name, 100, 'Nome da Pizza')
                ->validateRequired('description', $description, 'Descrição')
                ->validateMinLength('description', $description, 10, 'Descrição')
                ->validateMaxLength('description', $description, 500, 'Descrição')
                ->validateRequired('price', $price, 'Preço')
                ->validateRequired('category', $category, 'Categoria')
                ->validateMaxLength('category', $category, 50, 'Categoria');

            if (!empty($price) && !is_numeric($price)) {
                $validator->addError('price', 'O campo Preço deve ser um número válido.');
            } elseif (!empty($price) && (float) $price <= 0) {
                $validator->addError('price', 'O campo Preço deve ser maior que zero.');
            }

            if ($validator->hasErrors()) {
                self::$msg = $validator->getErrorsAsString();
                self::$fieldsWithErrors = $validator->getFieldsWithErrors();

                $pizza = new Pizza(
                    $id,
                    $validator->hasFieldError('name') ? '' : $name,
                    $validator->hasFieldError('description') ? '' : $description,
                    $validator->hasFieldError('price') ? 0.0 : (float) $price,
                    $validator->hasFieldError('category') ? '' : $category
                );

                self::$formData = [
                    'name' => $validator->hasFieldError('name') ? '' : $name,
                    'description' => $validator->hasFieldError('description') ? '' : $description,
                    'price' => $validator->hasFieldError('price') ? '' : $price,
                    'category' => $validator->hasFieldError('category') ? '' : $category
                ];
            } else {
                try {
                    $pizza = new Pizza($id, $name, $description, (float) $price, $category);
                    PizzaDao::update($pizza);
                    // Regenerar token após sucesso
                    CsrfToken::regenerate();
                    header("Location: ?page=pizzas");
                    exit;
                } catch (\Exception $e) {
                    self::$msg = $e->getMessage();
                    $pizza = new Pizza($id, $name, $description, (float) $price, $category);
                    self::$formData = [
                        'name' => $name,
                        'description' => $description,
                        'price' => $price,
                        'category' => $category
                    ];
                }
            }
        }

        PizzaView::renderForm(self::$msg, $pizza, self::$fieldsWithErrors, self::$formData);
    }

    public static function findById(int $id): ?Pizza
    {
        return PizzaDao::findById($id);
    }

    public static function findByCategory(string $category): array
    {
        return PizzaDao::findByCategory($category);
    }

    public static function delete(): void
    {
        if (isset($_GET["confirm"])) {
            $deleteId = (int) $_GET["confirm"];
            self::findAll($deleteId);
            return;
        }

        if (isset($_GET["action"]) && $_GET["action"] === "delete" && isset($_GET["id"])) {
            try {
                PizzaDao::delete((int) $_GET["id"]);
                header("Location: ?page=pizzas");
                exit;
            } catch (\Exception $e) {
                self::$msg = $e->getMessage();
                self::findAll();
            }
            return;
        }

        self::findAll();
    }

    public static function findAll(?int $deleteId = null): void
    {
        $pizzas = PizzaDao::findAll();
        PizzaView::renderList($pizzas, $deleteId);
    }

    public static function getCategories(): array
    {
        return PizzaDao::getCategories();
    }

    public static function getMenuPizzas(): array
    {
        $pizzas = PizzaDao::findAll();
        $pizzasByCategory = [];

        foreach ($pizzas as $pizza) {
            $pizzasByCategory[$pizza->category][] = $pizza;
        }

        return $pizzasByCategory;
    }

    public static function menu(): void
    {
        $pizzasByCategory = self::getMenuPizzas();
        PizzaView::renderPublicMenu($pizzasByCategory);
    }
}
