<?php
$sAdminNav = '';
$sControllerNav = '';
$iGroup = count($_SESSION['ControllerGroups']);
$sPageTitle = 'Limbonia Admin';
$system_menu = '';
$profile_menu = '';

if ($app->user()->isAdmin())
{
  $system_menu = '<a class="model" href="' . $app->generateUri('system')  . '">System</a> | ';
}

if (isset($_SESSION['ResourceList']['Profile']) && isset($app->activeControllers()['profile']))
{
  $profile_menu = '<a class="model" href="' . $app->generateUri('profile') . '">Profile</a> | ';
}

if ($iGroup > 0)
{
  $iMinGroups = array_key_exists('Hidden', $_SESSION['ControllerGroups']) ? 2 : 1;

  foreach ($_SESSION['ControllerGroups'] as $sGroup => $hControllerList)
  {
    if ($iGroup > $iMinGroups && $sGroup !== 'Hidden')
    {
      $sAdminNav .= "      <div class=\"controllerGroup\">$sGroup</div>\n";
    }

    foreach ($hControllerList as $sLabel => $sControllerName)
    {
      $sLowerController = strtolower($sControllerName);
      $oController = $app->controllerFactory($sControllerName);

      if ($sGroup !== 'Hidden')
      {
        $sAdminNav .= "      <div class=\"controller $sLowerController\" style=\"display: none\">\n";
        $sAdminNav .= "        <div class=\"title\">" . preg_replace("/([A-Z])/", " $1", $oController->getType()) . "</div>\n";

        $hQuickSearch = $oController->getQuickSearch();

        if (!empty($hQuickSearch) && $oController->allow('search'))
        {
          $sControllerType = $oController->getType();

          foreach ($hQuickSearch as $sColumn => $sTitle)
          {
            $sAdminNav .= "        <form name=\"QuickSearch\" action=\"" . $oController->generateUri('search', 'quick') . "\" method=\"post\">$sTitle:<input type=\"text\" name=\"{$sControllerType}[{$sColumn}]\" id=\"{$sControllerType}{$sColumn}\"></form>\n";
          }
        }

        $sAdminNav .= "      </div>\n";
        $sAdminNav .= "      <a class=\"$sLowerController\" href=\"" . $app->generateUri($sLabel) . "\">" . preg_replace("/([A-Z])/", " $1", $sControllerName) . "</a>\n";
      }

      foreach ($oController->getMenuItems() as $sMenuAction => $sMenuTitle)
      {
        if (!$oController->allow($sMenuAction))
        {
          continue;
        }

        if ($sMenuAction !== 'model')
        {
          $sCurrent = isset($method) && $method == $sMenuAction ? 'current ' : '';
          $sDisplay = isset($controller) && $oController->getType() == $controller->getType() ? '' : ' style="display: none"';
          $sControllerNav .= "        <a class=\"model {$sCurrent}tab $sLowerController $sMenuAction\"$sDisplay href=\"" . $oController->generateUri($sMenuAction) . "\">$sMenuTitle</a>\n";
        }
      }
    }
  }
}

if (empty($sAdminNav))
{
  $sAdminNav = "No controllers were found!<br>Try either: <a href=\"" . $app->generateUri('setup') . "\">Setup</a><a href=\"" . $app->generateUri('system', 'managecontrollers') . "\">Manage Controllers</a>";
}

if (!empty($content))
{
  $sTemp = $content;
  $content = "<script type=\"text/javascript\">
 updateAdminNav('" . $controller->getType() . "');\n";

  if (method_exists($controller, 'getModel') && $controller->getModel()->id > 0)
  {
    $content .= "   buildModel(" . json_encode($controller->getAdminOutput()) . ");
 $('#model > #page').html(" . json_encode($sTemp) . ");\n";
  }
  else
  {
    $sPageTitle = ucwords($controller->getType() . " > $method");
    $content .= "   $('#controllerOutput').html(" . json_encode($sTemp) . ");\n";
  }

  $content .= "\n</script>\n";
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="author" content="Lonnie Blansett">
  <meta name="generator" content="Limbonia <?= \Limbonia\App::version() ?>">
  <title><?= $sPageTitle ?></title>
  <link rel="stylesheet" type="text/css" href="<?= $app->domain->uri . '/' . $app->getDir('share') ?>/admin.css" />
<?php
if ($app->debug)
{
  echo "  <script type=\"text/javascript\" src=\"" . $app->domain->uri . '/' . $app->getDir('share') . "/node_modules/jquery/dist/jquery.js\"></script>\n";
  echo "  <script type=\"text/javascript\" src=\"" . $app->domain->uri . '/' . $app->getDir('share') . "/node_modules/slideout/dist/slideout.js\"></script>\n";
  echo "  <script type=\"text/javascript\" src=\"" . $app->domain->uri . '/' . $app->getDir('share') . "/admin.js\"></script>\n";
  echo "  <script type=\"text/javascript\" src=\"" . $app->domain->uri . '/' . $app->getDir('share') . "/ajax.js\"></script>\n";
  echo "  <script type=\"text/javascript\" src=\"" . $app->domain->uri . '/' . $app->getDir('share') . "/select.js\"></script>\n";
  echo "  <script type=\"text/javascript\" src=\"" . $app->domain->uri . '/' . $app->getDir('share') . "/sorttable.js\"></script>\n";
  echo "  <script type=\"text/javascript\" src=\"" . $app->domain->uri . '/' . $app->getDir('share') . "/window.js\"></script>\n";
}
else
{
  echo "  <script type=\"text/javascript\" src=\"" . $app->domain->uri . '/' . $app->getDir('share') . "/admin-all-min.js\"></script>\n";
}
?>
  <script type="text/javascript">
  $(function()
  {
    var slideout = new Slideout
    ({
      'panel': document.getElementById('content'),
      'menu': document.getElementById('menu'),
      'padding': 1,
      'tolerance': 70
    });
    $('.hamburger').on('click', function()
    {
      slideout.toggle();
    });
  });
  </script>
</head>
<body>

  <header>
    <span class="hamburger">☰</span>
    <span>User: <?= $app->oUser->name ?></span>
    <span class="tools"><?= $system_menu ?><?= $profile_menu ?><a href="<?= $app->generateUri('logout') ?>" target="_top">Logout</a></span>
  </header>

  <section id="admin">
    <nav class="controllerList" id="menu">
<?= $sAdminNav ?>
    </nav>
    <section id="content">
      <nav class="tabSet">
<?= $sControllerNav ?>
      </nav>
      <main id="controllerOutput">
<?= $content ?>
      </main>
    </section>
  </section>

</body>
</html>