<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Limbonia Password Check</title>
<?php
$sDefaultCSSFile = $app->getDir('root') . '/share/login.css';

if (is_readable($sDefaultCSSFile))
{
  echo "  <style>\n";
  echo file_get_contents($sDefaultCSSFile);
  echo "  </style>\n";
}
else
{
  echo '  <link rel="stylesheet" type="text/css" href="' . $app->domain->uri . '/' . $app->getDir('share') . '/login.css" />';
}
?>
</head>
<body onLoad="document.passCheck.email.focus();">
  <form action="" method="post" name="passCheck">
<?= (isset($failure) ? $failure : '') ?>
    <div class="field"><span class="name">Email:</span><span class="value"><input type="text" name="email"></span></div>
    <div class="field"><span class="name">Password:</span><span class="value"><input type="password" name="password"></span></div>
    <div class="field"><span class="name"></span><span class="value"><input type="submit" name="submit" value="Authorization"></span></div>
  </form>
</body>
</html>