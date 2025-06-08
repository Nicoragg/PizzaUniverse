<?php

namespace App\Dal;

use App\Dal\Connection;
use App\Models\Customer;
use Exception;
use \PDO;
use \PDOException;

abstract class CustomerDao
{
    public static function create(Customer $customer): int
    {
        try {
            $pdo = Connection::getConnection();
            $stmt = $pdo->prepare("INSERT INTO customers (name, cpf, phone, status, zipcode, neighborhood, street, city, state) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $customer->name,
                $customer->cpf,
                $customer->phone,
                $customer->status,
                $customer->zipcode,
                $customer->neighborhood,
                $customer->street,
                $customer->city,
                $customer->state
            ]);
            return (int) $pdo->lastInsertId();
        } catch (\PDOException $e) {
            throw new \PDOException("Erro ao salvar cliente no Banco de Dados: " . $e->getMessage());
        }
    }

    public static function findAll(): array
    {
        try {
            $pdo = Connection::getConnection();
            $stmt = $pdo->prepare("SELECT * FROM customers ORDER BY name");
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $customers = [];
            foreach ($res as $data) {
                $customers[] = new Customer(
                    (int) $data["id"],
                    $data["name"],
                    $data["cpf"],
                    $data["phone"],
                    $data["status"],
                    $data["zipcode"],
                    $data["neighborhood"],
                    $data["street"],
                    $data["city"],
                    $data["state"]
                );
            }

            return $customers;
        } catch (\PDOException $e) {
            throw new PDOException("Erro ao listar clientes: " . $e->getMessage());
        }
    }

    public static function findById(int $id): ?Customer
    {
        try {
            $pdo = Connection::getConnection();
            $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
            $stmt->execute([$id]);

            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$data) return null;

            return new Customer(
                (int) $data["id"],
                $data["name"],
                $data["cpf"],
                $data["phone"],
                $data["status"],
                $data["zipcode"],
                $data["neighborhood"],
                $data["street"],
                $data["city"],
                $data["state"]
            );
        } catch (\PDOException $e) {
            throw new PDOException("Erro ao buscar cliente: " . $e->getMessage());
        }
    }

    public static function findByCpf(string $cpf): ?Customer
    {
        try {
            $pdo = Connection::getConnection();
            $stmt = $pdo->prepare("SELECT * FROM customers WHERE cpf = ?");
            $stmt->execute([$cpf]);

            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$data) return null;

            return new Customer(
                (int) $data["id"],
                $data["name"],
                $data["cpf"],
                $data["phone"],
                $data["status"],
                $data["zipcode"],
                $data["neighborhood"],
                $data["street"],
                $data["city"],
                $data["state"]
            );
        } catch (\PDOException $e) {
            throw new PDOException("Erro ao buscar cliente por CPF: " . $e->getMessage());
        }
    }

    public static function findByStatus(string $status): array
    {
        try {
            $pdo = Connection::getConnection();
            $stmt = $pdo->prepare("SELECT * FROM customers WHERE status = ? ORDER BY name");
            $stmt->execute([$status]);
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $customers = [];
            foreach ($res as $data) {
                $customers[] = new Customer(
                    (int) $data["id"],
                    $data["name"],
                    $data["cpf"],
                    $data["phone"],
                    $data["status"],
                    $data["zipcode"],
                    $data["neighborhood"],
                    $data["street"],
                    $data["city"],
                    $data["state"]
                );
            }

            return $customers;
        } catch (\PDOException $e) {
            throw new PDOException("Erro ao buscar clientes por status: " . $e->getMessage());
        }
    }

    public static function update(Customer $customer): void
    {
        try {
            $pdo = Connection::getConnection();
            $stmt = $pdo->prepare("UPDATE customers SET name = ?, cpf = ?, phone = ?, status = ?, zipcode = ?, neighborhood = ?, street = ?, city = ?, state = ? WHERE id = ?");
            $stmt->execute([
                $customer->name,
                $customer->cpf,
                $customer->phone,
                $customer->status,
                $customer->zipcode,
                $customer->neighborhood,
                $customer->street,
                $customer->city,
                $customer->state,
                $customer->id
            ]);
        } catch (\PDOException $e) {
            throw new PDOException("Erro ao alterar cliente: " . $e->getMessage());
        }
    }

    public static function delete(int $id): void
    {
        try {
            $pdo = Connection::getConnection();
            $stmt = $pdo->prepare("DELETE FROM customers WHERE id = ?");
            $stmt->execute([$id]);

            if ($stmt->rowCount() === 0) {
                throw new Exception("Nenhum registro foi excluído ou cliente não encontrado");
            }
        } catch (\PDOException $e) {
            throw new PDOException("Erro ao excluir cliente: " . $e->getMessage());
        }
    }

    public static function getStates(): array
    {
        try {
            $pdo = Connection::getConnection();
            $stmt = $pdo->prepare("SELECT DISTINCT state FROM customers ORDER BY state");
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_COLUMN);

            return $res ?: [];
        } catch (\PDOException $e) {
            throw new PDOException("Erro ao buscar estados: " . $e->getMessage());
        }
    }

    public static function getCities(): array
    {
        try {
            $pdo = Connection::getConnection();
            $stmt = $pdo->prepare("SELECT DISTINCT city FROM customers ORDER BY city");
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_COLUMN);

            return $res ?: [];
        } catch (\PDOException $e) {
            throw new PDOException("Erro ao buscar cidades: " . $e->getMessage());
        }
    }
}
