<?php

namespace App\Util;

function validateInput($str): string
{
  return htmlspecialchars(trim($str));
}

function maskCpf(string $cpf): string
{
  $cpf = preg_replace('/[^0-9]/', '', $cpf);

  if (strlen($cpf) !== 11) {
    return $cpf;
  }

  return substr($cpf, 0, 3) . '-XXX-XXX-' . substr($cpf, -2);
}

function maskPhone(string $phone): string
{
  $phone = preg_replace('/[^0-9]/', '', $phone);

  if (strlen($phone) === 10) {
    return '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 4) . '-' . substr($phone, 6);
  } elseif (strlen($phone) === 11) {
    return '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 5) . '-' . substr($phone, 7);
  }

  return $phone;
}

class CsrfToken
{
  private const TOKEN_NAME = 'csrf_token';
  private const TOKEN_LIFETIME = 3600; // 1 hora

  public static function generate(): string
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }

    $token = bin2hex(random_bytes(32));
    $timestamp = time();

    $_SESSION[self::TOKEN_NAME] = [
      'token' => $token,
      'timestamp' => $timestamp
    ];

    return $token;
  }

  public static function validate(string $token): bool
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }

    if (!isset($_SESSION[self::TOKEN_NAME])) {
      return false;
    }

    $sessionToken = $_SESSION[self::TOKEN_NAME];

    // Verificar se o token expirou
    if ((time() - $sessionToken['timestamp']) > self::TOKEN_LIFETIME) {
      unset($_SESSION[self::TOKEN_NAME]);
      return false;
    }

    // Verificar se o token coincide (usando hash_equals para prevenir timing attacks)
    if (!hash_equals($sessionToken['token'], $token)) {
      return false;
    }

    return true;
  }

  public static function getTokenName(): string
  {
    return self::TOKEN_NAME;
  }

  public static function destroy(): void
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }

    unset($_SESSION[self::TOKEN_NAME]);
  }

  public static function regenerate(): string
  {
    self::destroy();
    return self::generate();
  }
}

class Validator
{
  private array $errors = [];
  private array $fieldsWithErrors = [];

  public function validateRequired(string $field, mixed $value, string $fieldName): self
  {
    if (empty($value) || trim($value) === '') {
      $this->errors[$field] = "O campo {$fieldName} é obrigatório.";
      $this->fieldsWithErrors[] = $field;
    }
    return $this;
  }

  public function validateEmail(string $field, string $value, string $fieldName): self
  {
    if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
      $this->errors[$field] = "O campo {$fieldName} deve conter um email válido.";
      $this->fieldsWithErrors[] = $field;
    }
    return $this;
  }

  public function validateMinLength(string $field, string $value, int $minLength, string $fieldName): self
  {
    if (!empty($value) && strlen($value) < $minLength) {
      $this->errors[$field] = "O campo {$fieldName} deve ter pelo menos {$minLength} caracteres.";
      $this->fieldsWithErrors[] = $field;
    }
    return $this;
  }

  public function validateMaxLength(string $field, string $value, int $maxLength, string $fieldName): self
  {
    if (!empty($value) && strlen($value) > $maxLength) {
      $this->errors[$field] = "O campo {$fieldName} deve ter no máximo {$maxLength} caracteres.";
      $this->fieldsWithErrors[] = $field;
    }
    return $this;
  }

  public function validateUniqueEmail(string $field, string $email, ?int $excludeId = null): self
  {
    if (!empty($email)) {
      $existingUser = \App\Dal\UserDao::findByEmail($email);
      if ($existingUser && ($excludeId === null || $existingUser->id !== $excludeId)) {
        $this->errors[$field] = "Este email já está sendo usado por outro usuário.";
        $this->fieldsWithErrors[] = $field;
      }
    }
    return $this;
  }

  public function validateCpf(string $field, string $cpf): self
  {
    if (!empty($cpf)) {
      $cpf = preg_replace('/[^0-9]/', '', $cpf);

      if (strlen($cpf) !== 11) {
        $this->errors[$field] = "CPF deve ter 11 dígitos.";
        $this->fieldsWithErrors[] = $field;
        return $this;
      }

      if (preg_match('/(\d)\1{10}/', $cpf)) {
        $this->errors[$field] = "CPF inválido.";
        $this->fieldsWithErrors[] = $field;
        return $this;
      }

      $sum = 0;
      for ($i = 0; $i < 9; $i++) {
        $sum += intval($cpf[$i]) * (10 - $i);
      }
      $remainder = $sum % 11;
      $digit1 = $remainder < 2 ? 0 : 11 - $remainder;

      if (intval($cpf[9]) !== $digit1) {
        $this->errors[$field] = "CPF inválido.";
        $this->fieldsWithErrors[] = $field;
        return $this;
      }

      $sum = 0;
      for ($i = 0; $i < 10; $i++) {
        $sum += intval($cpf[$i]) * (11 - $i);
      }
      $remainder = $sum % 11;
      $digit2 = $remainder < 2 ? 0 : 11 - $remainder;

      if (intval($cpf[10]) !== $digit2) {
        $this->errors[$field] = "CPF inválido.";
        $this->fieldsWithErrors[] = $field;
      }
    }
    return $this;
  }

  public function validateUniqueCpf(string $field, string $cpf, ?int $excludeId = null): self
  {
    if (!empty($cpf)) {
      $cpf = preg_replace('/[^0-9]/', '', $cpf);
      $existingCustomer = \App\Dal\CustomerDao::findByCpf($cpf);
      if ($existingCustomer && ($excludeId === null || $existingCustomer->id !== $excludeId)) {
        $this->errors[$field] = "Este CPF já está sendo usado por outro cliente.";
        $this->fieldsWithErrors[] = $field;
      }
    }
    return $this;
  }

  public function validatePhone(string $field, string $phone): self
  {
    if (!empty($phone)) {
      $phone = preg_replace('/[^0-9]/', '', $phone);

      if (strlen($phone) < 10 || strlen($phone) > 11) {
        $this->errors[$field] = "Telefone deve ter 10 ou 11 dígitos.";
        $this->fieldsWithErrors[] = $field;
      }
    }
    return $this;
  }

  public function validateZipcode(string $field, string $zipcode): self
  {
    if (!empty($zipcode)) {
      $zipcode = preg_replace('/[^0-9]/', '', $zipcode);

      if (strlen($zipcode) !== 8) {
        $this->errors[$field] = "CEP deve ter 8 dígitos.";
        $this->fieldsWithErrors[] = $field;
      }
    }
    return $this;
  }

  public function addError(string $field, string $message): self
  {
    $this->errors[$field] = $message;
    $this->fieldsWithErrors[] = $field;
    return $this;
  }

  public function hasErrors(): bool
  {
    return !empty($this->errors);
  }

  public function getErrors(): array
  {
    return $this->errors;
  }

  public function getFirstError(): ?string
  {
    return !empty($this->errors) ? reset($this->errors) : null;
  }

  public function getErrorsAsString(): string
  {
    return implode(' ', $this->errors);
  }

  public function hasFieldError(string $field): bool
  {
    return in_array($field, $this->fieldsWithErrors);
  }

  public function getFieldsWithErrors(): array
  {
    return array_unique($this->fieldsWithErrors);
  }

  public function validateUniquePhone(string $field, string $phone, ?int $excludeId = null): self
  {
    if (!empty($phone)) {
      $phone = preg_replace('/[^0-9]/', '', $phone);
      $existingCustomer = \App\Dal\CustomerDao::findByPhone($phone);
      if ($existingCustomer && ($excludeId === null || $existingCustomer->id !== $excludeId)) {
        $this->errors[$field] = "Este telefone já está sendo usado por outro cliente.";
        $this->fieldsWithErrors[] = $field;
      }
    }
    return $this;
  }
}
