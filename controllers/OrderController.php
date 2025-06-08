<?php

namespace App\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Dal\OrderDao;
use App\Dal\CustomerDao;
use App\Dal\PizzaDao;
use App\Views\OrderView;
use App\Util\Validator;
use App\Util\CsrfToken;
use function App\Util\validateInput;

abstract class OrderController
{
    public static ?string $msg = null;
    public static ?array $fieldsWithErrors = null;
    public static ?array $formData = null;

    public static function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $token = validateInput($_POST[CsrfToken::getTokenName()] ?? '');
            if (!CsrfToken::validate($token)) {
                self::$msg = "Token de segurança inválido. Por favor, tente novamente.";
                self::renderCreateForm();
                return;
            }

            $customerId = (int) validateInput($_POST["customer_id"] ?? '');
            $deliveryAddress = validateInput($_POST["delivery_address"] ?? '');
            $notes = validateInput($_POST["notes"] ?? '');
            $pizzas = $_POST["pizzas"] ?? [];

            $validator = new Validator();
            $validator->validateRequired('customer_id', $customerId, 'Cliente');

            // Validar se pelo menos uma pizza foi selecionada
            $validPizzas = [];
            $totalAmount = 0.0;

            foreach ($pizzas as $pizzaId => $quantity) {
                $quantity = (int) $quantity;
                if ($quantity > 0) {
                    $pizza = PizzaDao::findById((int) $pizzaId);
                    if ($pizza) {
                        $validPizzas[] = [
                            'pizza_id' => $pizza->id,
                            'quantity' => $quantity,
                            'unit_price' => $pizza->price,
                            'subtotal' => $quantity * $pizza->price
                        ];
                        $totalAmount += $quantity * $pizza->price;
                    }
                }
            }

            if (empty($validPizzas)) {
                $validator->addError('pizzas', 'Selecione pelo menos uma pizza para o pedido.');
            }

            if ($validator->hasErrors()) {
                self::$msg = $validator->getErrorsAsString();
                self::$fieldsWithErrors = $validator->getFieldsWithErrors();
                self::$formData = [
                    'customer_id' => $customerId,
                    'delivery_address' => $deliveryAddress,
                    'notes' => $notes,
                    'pizzas' => $pizzas
                ];
            } else {
                try {
                    $orderNumber = OrderDao::generateOrderNumber();

                    $order = new Order(
                        0,
                        $customerId,
                        $orderNumber,
                        'pending',
                        $totalAmount,
                        $deliveryAddress ?: null,
                        $notes ?: null
                    );

                    $orderItems = [];
                    foreach ($validPizzas as $pizzaData) {
                        $orderItems[] = new OrderItem(
                            0,
                            0, // será definido após criar o pedido
                            $pizzaData['pizza_id'],
                            $pizzaData['quantity'],
                            $pizzaData['unit_price'],
                            $pizzaData['subtotal']
                        );
                    }

                    $orderId = OrderDao::create($order, $orderItems);
                    CsrfToken::regenerate();
                    header("Location: ?page=orders");
                    exit;
                } catch (\Exception $e) {
                    self::$msg = $e->getMessage();
                }
            }
        }

        self::renderCreateForm();
    }

    private static function renderCreateForm(): void
    {
        $customers = CustomerDao::findByStatus('active');
        $pizzas = PizzaDao::findAll();

        // Organizar pizzas por categoria
        $pizzasByCategory = [];
        foreach ($pizzas as $pizza) {
            $pizzasByCategory[$pizza->category][] = $pizza;
        }

        OrderView::renderCreateForm(self::$msg, $customers, $pizzasByCategory, self::$formData);
    }

    public static function findAll(): void
    {
        $orders = OrderDao::findAll();
        OrderView::renderList($orders);
    }

    public static function view(): void
    {
        $orderId = (int) ($_GET['id'] ?? 0);

        if ($orderId === 0) {
            header("Location: ?page=orders");
            exit;
        }

        $order = OrderDao::findById($orderId);
        if (!$order) {
            self::$msg = "Pedido não encontrado.";
            self::findAll();
            return;
        }

        $orderItems = OrderDao::getOrderItems($orderId);
        $customer = CustomerDao::findById($order->customerId);

        if (!$customer) {
            self::$msg = "Cliente do pedido não encontrado.";
            self::findAll();
            return;
        }

        OrderView::renderDetails($order, $customer, $orderItems);
    }

    public static function updateStatus(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
            $token = validateInput($_POST[CsrfToken::getTokenName()] ?? '');
            if (!CsrfToken::validate($token)) {
                self::$msg = "Token de segurança inválido. Por favor, tente novamente.";
                self::findAll();
                return;
            }

            $orderId = (int) $_POST['order_id'];
            $newStatus = $_POST['status'];
            $redirect = $_POST['redirect'] ?? '';

            $validStatuses = array_keys(OrderDao::getOrderStatuses());
            if (!in_array($newStatus, $validStatuses)) {
                self::$msg = "Status inválido.";
                self::findAll();
                return;
            }

            try {
                $order = OrderDao::findById($orderId);
                if (!$order) {
                    self::$msg = "Pedido não encontrado.";
                    self::findAll();
                    return;
                }

                OrderDao::updateStatus($orderId, $newStatus);

                if (!empty($redirect)) {
                    header("Location: ?page=orders&action=" . $redirect);
                } else {
                    header("Location: ?page=orders");
                }
                exit;
            } catch (\Exception $e) {
                self::$msg = $e->getMessage();
                self::findAll();
            }
            return;
        }

        self::findAll();
    }

    public static function delete(): void
    {
        if (isset($_GET["confirm"])) {
            $deleteId = (int) $_GET["confirm"];
            // Aqui deveria mostrar confirmação, mas vamos simplificar
            self::findAll();
            return;
        }

        if (isset($_GET["action"]) && $_GET["action"] === "delete" && isset($_GET["id"])) {
            try {
                OrderDao::delete((int) $_GET["id"]);
                header("Location: ?page=orders");
                exit;
            } catch (\Exception $e) {
                self::$msg = $e->getMessage();
                self::findAll();
            }
            return;
        }

        self::findAll();
    }

    public static function findById(int $id): ?Order
    {
        return OrderDao::findById($id);
    }

    public static function findByCustomerId(int $customerId): array
    {
        return OrderDao::findByCustomerId($customerId);
    }

    public static function findByStatus(string $status): array
    {
        return OrderDao::findByStatus($status);
    }

    public static function getOrderItems(int $orderId): array
    {
        return OrderDao::getOrderItems($orderId);
    }
}
