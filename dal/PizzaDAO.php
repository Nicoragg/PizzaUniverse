<?php

namespace App\Dal;

use App\Dal\Connection;
use App\Models\Pizza;
use Exception;
use \PDO;
use \PDOException;

abstract class PizzaDao
{
    public static function create(Pizza $pizza): int
    {
        try {
            $pdo = Connection::getConnection();
            $stmt = $pdo->prepare("INSERT INTO pizzas (name, description, price, category) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $pizza->name,
                $pizza->description,
                $pizza->price,
                $pizza->category
            ]);
            return (int) $pdo->lastInsertId();
        } catch (\PDOException $e) {
            throw new \PDOException("Erro ao salvar pizza no Banco de Dados: " . $e->getMessage());
        }
    }

    public static function findAll(): array
    {
        try {
            $pdo = Connection::getConnection();
            $stmt = $pdo->prepare("SELECT * FROM pizzas ORDER BY category, name");
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $pizzas = [];
            foreach ($res as $data) {
                $pizzas[] = new Pizza(
                    (int) $data["id"],
                    $data["name"],
                    $data["description"],
                    (float) $data["price"],
                    $data["category"]
                );
            }

            return $pizzas;
        } catch (\PDOException $e) {
            throw new PDOException("Erro ao listar pizzas: " . $e->getMessage());
        }
    }

    public static function findById(int $id): ?Pizza
    {
        try {
            $pdo = Connection::getConnection();
            $stmt = $pdo->prepare("SELECT * FROM pizzas WHERE id = ?");
            $stmt->execute([$id]);

            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$data) return null;

            return new Pizza(
                (int) $data["id"],
                $data["name"],
                $data["description"],
                (float) $data["price"],
                $data["category"]
            );
        } catch (\PDOException $e) {
            throw new PDOException("Erro ao buscar pizza: " . $e->getMessage());
        }
    }

    public static function findByCategory(string $category): array
    {
        try {
            $pdo = Connection::getConnection();
            $stmt = $pdo->prepare("SELECT * FROM pizzas WHERE category = ? ORDER BY name");
            $stmt->execute([$category]);
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $pizzas = [];
            foreach ($res as $data) {
                $pizzas[] = new Pizza(
                    (int) $data["id"],
                    $data["name"],
                    $data["description"],
                    (float) $data["price"],
                    $data["category"]
                );
            }

            return $pizzas;
        } catch (\PDOException $e) {
            throw new PDOException("Erro ao buscar pizzas por categoria: " . $e->getMessage());
        }
    }

    public static function update(Pizza $pizza): void
    {
        try {
            $pdo = Connection::getConnection();
            $stmt = $pdo->prepare("UPDATE pizzas SET name = ?, description = ?, price = ?, category = ? WHERE id = ?");
            $stmt->execute([
                $pizza->name,
                $pizza->description,
                $pizza->price,
                $pizza->category,
                $pizza->id
            ]);
        } catch (\PDOException $e) {
            throw new PDOException("Erro ao alterar pizza: " . $e->getMessage());
        }
    }

    public static function delete(int $id): void
    {
        try {
            $pdo = Connection::getConnection();
            $stmt = $pdo->prepare("DELETE FROM pizzas WHERE id = ?");
            $stmt->execute([$id]);

            if ($stmt->rowCount() === 0) {
                throw new Exception("Nenhum registro foi excluído ou pizza não encontrada");
            }
        } catch (\PDOException $e) {
            throw new PDOException("Erro ao excluir pizza: " . $e->getMessage());
        }
    }

    public static function getCategories(): array
    {
        try {
            $pdo = Connection::getConnection();
            $stmt = $pdo->prepare("SELECT DISTINCT category FROM pizzas ORDER BY category");
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_COLUMN);

            return $res ?: [];
        } catch (\PDOException $e) {
            throw new PDOException("Erro ao buscar categorias: " . $e->getMessage());
        }
    }
}
