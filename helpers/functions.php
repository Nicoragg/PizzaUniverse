<?php

namespace App\Util;

function validateInput($str): string
{
  return htmlspecialchars(trim($str));
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
      // Remove formatting
      $cpf = preg_replace('/[^0-9]/', '', $cpf);

      // Check if it has 11 digits
      if (strlen($cpf) !== 11) {
        $this->errors[$field] = "CPF deve ter 11 dígitos.";
        $this->fieldsWithErrors[] = $field;
        return $this;
      }

      // Check if all digits are the same
      if (preg_match('/(\d)\1{10}/', $cpf)) {
        $this->errors[$field] = "CPF inválido.";
        $this->fieldsWithErrors[] = $field;
        return $this;
      }

      // Validate CPF algorithm
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
      // Remove formatting
      $phone = preg_replace('/[^0-9]/', '', $phone);

      // Check if it has 10 or 11 digits (landline or mobile)
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
      // Remove formatting
      $zipcode = preg_replace('/[^0-9]/', '', $zipcode);

      // Check if it has 8 digits
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
}
