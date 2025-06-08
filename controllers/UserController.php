<?php

namespace App\Controllers;

use App\Models\User;
use App\Dal\UserDao;
use App\Views\UserView;
use App\Util\Validator;
use App\Util\CsrfToken;
use function App\Util\validateInput;

abstract class UserController
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
                UserView::renderForm(self::$msg, null, self::$fieldsWithErrors, self::$formData);
                return;
            }

            $username = validateInput($_POST["username"] ?? '');
            $email = validateInput($_POST["email"] ?? '');
            $password = validateInput($_POST["password"] ?? '');

            $validator = new Validator();
            $validator
                ->validateRequired('username', $username, 'Nome de usuário')
                ->validateMinLength('username', $username, 3, 'Nome de usuário')
                ->validateMaxLength('username', $username, 50, 'Nome de usuário')
                ->validateRequired('email', $email, 'Email')
                ->validateEmail('email', $email, 'Email')
                ->validateUniqueEmail('email', $email)
                ->validateRequired('password', $password, 'Senha')
                ->validateMinLength('password', $password, 6, 'Senha');

            if ($validator->hasErrors()) {
                self::$msg = $validator->getErrorsAsString();
                self::$fieldsWithErrors = $validator->getFieldsWithErrors();
                self::$formData = [
                    'username' => $validator->hasFieldError('username') ? '' : $username,
                    'email' => $validator->hasFieldError('email') ? '' : $email,
                    'password' => ''
                ];
            } else {
                try {
                    $user = new User(0, $username, $email, md5($password));
                    $id = UserDao::create($user);
                    CsrfToken::regenerate();
                    header("Location: ?page=users");
                    exit;
                } catch (\Exception $e) {
                    self::$msg = $e->getMessage();
                }
            }
        }
        UserView::renderForm(self::$msg, null, self::$fieldsWithErrors, self::$formData);
    }

    public static function update(): void
    {
        $user = null;
        if (isset($_GET["edit"])) {
            $user = UserDao::findById((int) $_GET["edit"]);
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $token = validateInput($_POST[CsrfToken::getTokenName()] ?? '');
            if (!CsrfToken::validate($token)) {
                self::$msg = "Token de segurança inválido. Por favor, tente novamente.";
                UserView::renderForm(self::$msg, $user, self::$fieldsWithErrors, self::$formData);
                return;
            }

            $id = (int) validateInput($_POST["id"] ?? '0');
            $username = validateInput($_POST["username"] ?? '');
            $email = validateInput($_POST["email"] ?? '');
            $password = validateInput($_POST["password"] ?? '');

            $validator = new Validator();
            $validator
                ->validateRequired('username', $username, 'Nome de usuário')
                ->validateMinLength('username', $username, 3, 'Nome de usuário')
                ->validateMaxLength('username', $username, 50, 'Nome de usuário')
                ->validateRequired('email', $email, 'Email')
                ->validateEmail('email', $email, 'Email')
                ->validateUniqueEmail('email', $email, $id);

            if (!empty($password)) {
                $validator->validateMinLength('password', $password, 6, 'Senha');
            }

            if ($validator->hasErrors()) {
                self::$msg = $validator->getErrorsAsString();
                self::$fieldsWithErrors = $validator->getFieldsWithErrors();

                $user = new User(
                    $id,
                    $validator->hasFieldError('username') ? '' : $username,
                    $validator->hasFieldError('email') ? '' : $email,
                    ''
                );

                self::$formData = [
                    'username' => $validator->hasFieldError('username') ? '' : $username,
                    'email' => $validator->hasFieldError('email') ? '' : $email,
                    'password' => ''
                ];
            } else {
                try {
                    $currentUser = UserDao::findById($id);
                    $finalPassword = !empty($password) ? md5($password) : $currentUser->password;

                    $user = new User($id, $username, $email, $finalPassword);
                    UserDao::update($user);
                    CsrfToken::regenerate();
                    header("Location: ?page=users");
                    exit;
                } catch (\Exception $e) {
                    self::$msg = $e->getMessage();
                    $user = new User($id, $username, $email, '');
                }
            }
        }

        UserView::renderForm(self::$msg, $user, self::$fieldsWithErrors, self::$formData);
    }

    public static function findById(int $id): ?User
    {
        return UserDao::findById($id);
    }

    public static function findByEmail(string $email): ?User
    {
        return UserDao::findByEmail($email);
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
                UserDao::delete((int) $_GET["id"]);
                header("Location: ?page=users");
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
        $users = UserDao::findAll();

        UserView::renderList($users, $deleteId);
    }

    public static function authenticate(string $email, string $password): ?User
    {
        return UserDao::authenticate($email, $password);
    }

    public static function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $token = validateInput($_POST[CsrfToken::getTokenName()] ?? '');
            if (!CsrfToken::validate($token)) {
                self::$msg = "Token de segurança inválido. Por favor, tente novamente.";
                UserView::renderLogin(self::$msg, self::$fieldsWithErrors, self::$formData);
                return;
            }

            $email = validateInput($_POST["email"] ?? '');
            $password = validateInput($_POST["password"] ?? '');

            $validator = new Validator();
            $validator
                ->validateRequired('email', $email, 'Email')
                ->validateEmail('email', $email, 'Email')
                ->validateRequired('password', $password, 'Senha');

            if ($validator->hasErrors()) {
                self::$msg = $validator->getErrorsAsString();
                self::$fieldsWithErrors = $validator->getFieldsWithErrors();
                self::$formData = [
                    'email' => $validator->hasFieldError('email') ? '' : $email,
                    'password' => ''
                ];
            } else {
                try {
                    $user = self::authenticate($email, $password);

                    if ($user) {
                        $_SESSION['user_id'] = $user->id;
                        $_SESSION['username'] = $user->username;
                        $_SESSION['email'] = $user->email;
                        CsrfToken::regenerate();
                        header("Location: ?page=home");
                        exit;
                    } else {
                        self::$msg = "Email ou senha incorretos";
                        self::$formData = ['email' => $email, 'password' => ''];
                    }
                } catch (\Exception $e) {
                    self::$msg = $e->getMessage();
                    self::$formData = ['email' => $email, 'password' => ''];
                }
            }
        }

        UserView::renderLogin(self::$msg, self::$fieldsWithErrors, self::$formData);
    }

    public static function logout(): void
    {
        session_start();
        session_destroy();
        header("Location: ?page=login");
        exit;
    }
}
