<?php

namespace App\Controllers;

use App\Models\Customer;
use App\Dal\CustomerDao;
use App\Views\CustomerView;
use App\Util\Validator;
use App\Util\CsrfToken;
use function App\Util\validateInput;

abstract class CustomerController
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
                CustomerView::renderForm(self::$msg, null, self::$fieldsWithErrors, self::$formData);
                return;
            }

            $name = validateInput($_POST["name"] ?? '');
            $cpf = preg_replace('/[^0-9]/', '', validateInput($_POST["cpf"] ?? ''));
            $phone = preg_replace('/[^0-9]/', '', validateInput($_POST["phone"] ?? ''));
            $status = 'active';
            $zipcode = validateInput($_POST["zipcode"] ?? '');
            $neighborhood = validateInput($_POST["neighborhood"] ?? '');
            $street = validateInput($_POST["street"] ?? '');
            $city = validateInput($_POST["city"] ?? '');
            $state = validateInput($_POST["state"] ?? '');

            $validator = new Validator();
            $validator
                ->validateRequired('name', $name, 'Nome')
                ->validateMinLength('name', $name, 2, 'Nome')
                ->validateMaxLength('name', $name, 100, 'Nome')
                ->validateRequired('cpf', $cpf, 'CPF')
                ->validateCpf('cpf', $cpf)
                ->validateUniqueCpf('cpf', $cpf)
                ->validateRequired('phone', $phone, 'Telefone')
                ->validatePhone('phone', $phone)
                ->validateUniquePhone('phone', $phone)
                ->validateRequired('zipcode', $zipcode, 'CEP')
                ->validateZipcode('zipcode', $zipcode)
                ->validateRequired('neighborhood', $neighborhood, 'Bairro')
                ->validateRequired('street', $street, 'Rua')
                ->validateRequired('city', $city, 'Cidade')
                ->validateRequired('state', $state, 'Estado');

            if ($validator->hasErrors()) {
                self::$msg = $validator->getErrorsAsString();
                self::$fieldsWithErrors = $validator->getFieldsWithErrors();
                self::$formData = [
                    'name' => $validator->hasFieldError('name') ? '' : $name,
                    'cpf' => $validator->hasFieldError('cpf') ? '' : $cpf,
                    'phone' => $validator->hasFieldError('phone') ? '' : $phone,
                    'status' => $status,
                    'zipcode' => $validator->hasFieldError('zipcode') ? '' : $zipcode,
                    'neighborhood' => $validator->hasFieldError('neighborhood') ? '' : $neighborhood,
                    'street' => $validator->hasFieldError('street') ? '' : $street,
                    'city' => $validator->hasFieldError('city') ? '' : $city,
                    'state' => $validator->hasFieldError('state') ? '' : $state
                ];
            } else {
                try {
                    $customer = new Customer(0, $name, $cpf, $phone, $status, $zipcode, $neighborhood, $street, $city, $state);
                    $id = CustomerDao::create($customer);
                    CsrfToken::regenerate();
                    header("Location: ?page=customers");
                    exit;
                } catch (\Exception $e) {
                    self::$msg = $e->getMessage();
                }
            }
        }
        CustomerView::renderForm(self::$msg, null, self::$fieldsWithErrors, self::$formData);
    }

    public static function update(): void
    {
        $customer = null;
        if (isset($_GET["edit"])) {
            $customer = CustomerDao::findById((int) $_GET["edit"]);
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $token = validateInput($_POST[CsrfToken::getTokenName()] ?? '');
            if (!CsrfToken::validate($token)) {
                self::$msg = "Token de segurança inválido. Por favor, tente novamente.";
                CustomerView::renderForm(self::$msg, $customer, self::$fieldsWithErrors, self::$formData);
                return;
            }

            $id = (int) validateInput($_POST["id"] ?? '0');
            $name = validateInput($_POST["name"] ?? '');
            $cpf = preg_replace('/[^0-9]/', '', validateInput($_POST["cpf"] ?? ''));
            $phone = preg_replace('/[^0-9]/', '', validateInput($_POST["phone"] ?? ''));
            $status = validateInput($_POST["status"] ?? 'active');
            $zipcode = validateInput($_POST["zipcode"] ?? '');
            $neighborhood = validateInput($_POST["neighborhood"] ?? '');
            $street = validateInput($_POST["street"] ?? '');
            $city = validateInput($_POST["city"] ?? '');
            $state = validateInput($_POST["state"] ?? '');

            $validator = new Validator();
            $validator
                ->validateRequired('name', $name, 'Nome')
                ->validateMinLength('name', $name, 2, 'Nome')
                ->validateMaxLength('name', $name, 100, 'Nome')
                ->validateRequired('cpf', $cpf, 'CPF')
                ->validateCpf('cpf', $cpf)
                ->validateUniqueCpf('cpf', $cpf, $id)
                ->validateRequired('phone', $phone, 'Telefone')
                ->validatePhone('phone', $phone)
                ->validateUniquePhone('phone', $phone, $id)
                ->validateRequired('zipcode', $zipcode, 'CEP')
                ->validateZipcode('zipcode', $zipcode)
                ->validateRequired('neighborhood', $neighborhood, 'Bairro')
                ->validateRequired('street', $street, 'Rua')
                ->validateRequired('city', $city, 'Cidade')
                ->validateRequired('state', $state, 'Estado');

            if ($validator->hasErrors()) {
                self::$msg = $validator->getErrorsAsString();
                self::$fieldsWithErrors = $validator->getFieldsWithErrors();

                $customer = new Customer(
                    $id,
                    $validator->hasFieldError('name') ? '' : $name,
                    $validator->hasFieldError('cpf') ? '' : $cpf,
                    $validator->hasFieldError('phone') ? '' : $phone,
                    $status,
                    $validator->hasFieldError('zipcode') ? '' : $zipcode,
                    $validator->hasFieldError('neighborhood') ? '' : $neighborhood,
                    $validator->hasFieldError('street') ? '' : $street,
                    $validator->hasFieldError('city') ? '' : $city,
                    $validator->hasFieldError('state') ? '' : $state
                );

                self::$formData = [
                    'name' => $validator->hasFieldError('name') ? '' : $name,
                    'cpf' => $validator->hasFieldError('cpf') ? '' : $cpf,
                    'phone' => $validator->hasFieldError('phone') ? '' : $phone,
                    'status' => $status,
                    'zipcode' => $validator->hasFieldError('zipcode') ? '' : $zipcode,
                    'neighborhood' => $validator->hasFieldError('neighborhood') ? '' : $neighborhood,
                    'street' => $validator->hasFieldError('street') ? '' : $street,
                    'city' => $validator->hasFieldError('city') ? '' : $city,
                    'state' => $validator->hasFieldError('state') ? '' : $state
                ];
            } else {
                try {
                    $customer = new Customer($id, $name, $cpf, $phone, $status, $zipcode, $neighborhood, $street, $city, $state);
                    CustomerDao::update($customer);
                    CsrfToken::regenerate();
                    header("Location: ?page=customers");
                    exit;
                } catch (\Exception $e) {
                    self::$msg = $e->getMessage();

                    $customer = new Customer($id, $name, $cpf, $phone, $status, $zipcode, $neighborhood, $street, $city, $state);
                    self::$formData = [
                        'name' => $name,
                        'cpf' => $cpf,
                        'phone' => $phone,
                        'status' => $status,
                        'zipcode' => $zipcode,
                        'neighborhood' => $neighborhood,
                        'street' => $street,
                        'city' => $city,
                        'state' => $state
                    ];
                }
            }
        }

        CustomerView::renderForm(self::$msg, $customer, self::$fieldsWithErrors, self::$formData);
    }

    public static function findById(int $id): ?Customer
    {
        return CustomerDao::findById($id);
    }

    public static function findByCpf(string $cpf): ?Customer
    {
        return CustomerDao::findByCpf($cpf);
    }

    public static function findByStatus(string $status): array
    {
        return CustomerDao::findByStatus($status);
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
                CustomerDao::delete((int) $_GET["id"]);
                header("Location: ?page=customers");
                exit;
            } catch (\Exception $e) {
                self::$msg = $e->getMessage();
                self::findAll();
            }
            return;
        }

        self::findAll();
    }

    public static function updateStatus(): void
    {
        if (isset($_GET["action"]) && $_GET["action"] === "status" && isset($_GET["id"]) && isset($_GET["status"])) {
            $customerId = (int) $_GET["id"];
            $newStatus = $_GET["status"];

            if (!in_array($newStatus, ['active', 'inactive'])) {
                self::$msg = "Status inválido.";
                self::findAll();
                return;
            }

            try {
                $customer = CustomerDao::findById($customerId);
                if (!$customer) {
                    self::$msg = "Cliente não encontrado.";
                    self::findAll();
                    return;
                }

                $updatedCustomer = new Customer(
                    $customer->id,
                    $customer->name,
                    $customer->cpf,
                    $customer->phone,
                    $newStatus,
                    $customer->zipcode,
                    $customer->neighborhood,
                    $customer->street,
                    $customer->city,
                    $customer->state
                );

                CustomerDao::update($updatedCustomer);

                header("Location: ?page=customers");
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
        $customers = CustomerDao::findAll();

        CustomerView::renderList($customers, $deleteId);
    }

    public static function getStates(): array
    {
        return CustomerDao::getStates();
    }

    public static function getCities(): array
    {
        return CustomerDao::getCities();
    }

    public static function findByPhone(string $phone): ?Customer
    {
        return CustomerDao::findByPhone($phone);
    }
}
