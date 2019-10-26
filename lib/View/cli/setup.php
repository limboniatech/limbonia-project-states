<?php
try
{
  echo "\nStart set-up:\n";
  $app->setup();
  die("\nFinish set-up\n");
}
catch (\Exception $e)
{
  die("Failed to generate stub Model class for $sTable: " . $e->getMessage());
}