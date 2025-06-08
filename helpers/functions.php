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
