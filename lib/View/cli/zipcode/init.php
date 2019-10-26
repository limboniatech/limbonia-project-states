<?php
try
{
  echo "Checking zipcode database: ";

  if ($app->getDB()->hasTable('ZipCode'))
  {
    die("yes!\nNothong more to do...\n");
  }

//  $controller

  output('Finished initializing zipcode database');
}
catch (\Exception $e)
{
  output('Failed to initialize zipcode database: ' . $e->getMessage());
}
