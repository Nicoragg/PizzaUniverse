<?php

namespace App\Controllers;

use App\Models\User;
use App\Dal\UserDao;
use function App\Util\validateInput;

abstract class UserController
{
    public static ?string $msg = null;

    public static function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST["username"])) {
            $username = validateInput($_POST["username"]);
            $email = validateInput($_POST["email"]);
            $password = validateInput($_POST["password"]);

            try {
                $user = new User(0, $username, $email, md5($password));

                $id = UserDao::create($user);
                header("Location: ?p=users");
            } catch (\Exception $e) {
                self::$msg = $e->getMessage();
            }
        }
        // UserView::formulario(self::$msg, null);
    }

    public static function update(): void
    {
        $user = null;
        if (isset($_GET["edit"])) {
            $user = UserDao::findById((int) $_GET["edit"]);
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
            $id = (int) validateInput($_POST["id"]);
            $username = validateInput($_POST["username"]);
            $email = validateInput($_POST["email"]);
            $password = validateInput($_POST["password"]);

            try {
                $user = new User($id, $username, $email, md5($password));
                UserDao::update($user);
                header("Location: ?p=users");
                exit;
            } catch (\Exception $e) {
                self::$msg = $e->getMessage();
            }
        }

        // UserView::formulario(self::$msg, $user);
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
        if (isset($_GET["del"])) {
            self::findAll((int) $_GET["del"]);
        }
        if (isset($_GET["delete"])) {
            UserDao::delete((int) $_GET["delete"]);
            header("Location: ?p=users");
            exit;
        }
    }

    public static function findAll(?int $deleteId = null): void
    {
        $users = UserDao::findAll();

        // UserView::listar($users, $deleteId);
    }

    public static function authenticate(string $email, string $password): ?User
    {
        return UserDao::authenticate($email, $password);
    }

    public static function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST["email"], $_POST["password"])) {
            $email = validateInput($_POST["email"]);
            $password = validateInput($_POST["password"]);

            try {
                $user = self::authenticate($email, $password);

                if ($user) {
                    session_start();
                    $_SESSION['user_id'] = $user->id;
                    $_SESSION['username'] = $user->username;
                    $_SESSION['email'] = $user->email;
                    header("Location: ?p=dashboard");
                    exit;
                } else {
                    self::$msg = "Email ou senha incorretos";
                }
            } catch (\Exception $e) {
                self::$msg = $e->getMessage();
            }
        }

        // UserView::login(self::$msg);
    }

    public static function logout(): void
    {
        session_start();
        session_destroy();
        header("Location: ?p=login");
        exit;
    }
}
