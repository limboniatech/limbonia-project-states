<?php
try
{
  $sTable = $options['t'] ?? $options['table'] ?? null;
  die($controller->generateModelCode($sTable));
}
catch (\Exception $e)
{
  die("Failed to generate stub Model class for $sTable: " . $e->getMessage());
}