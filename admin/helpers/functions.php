<?php

namespace App\Util;

function validateInput($str): string
{
  return htmlspecialchars(trim($str));
}
