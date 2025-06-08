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
