<?php
function validateInput($str): string
{
  return htmlspecialchars(trim($str));
}
