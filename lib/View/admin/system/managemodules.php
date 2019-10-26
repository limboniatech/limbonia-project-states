<?php
if (isset($error))
{
  foreach ($error as $i => $sError)
  {
    \Limbonia\Widget::warningText($sError);
  }
}

$hAvailableControllerList = $app->availableControllers();
$hActiveControllers = $app->activeControllers();

$table = $app->widgetFactory('table');
$table->makeSortable();
$table->startHeader();
$table->addCell('Active');
$table->addCell('Name');
$table->endRow();

foreach ($hAvailableControllerList as $sAvailableDriver => $sAvailableName)
{
  $sDriver = \Limbonia\Controller::driver($sAvailableName);
  $sTypeClass = '\\Limbonia\\Controller\\' . $sDriver;
  $sChecked = isset($hActiveControllers[$sAvailableDriver]) ? ' checked' : '';
  $table->startRow();
  $table->addCell("<input type=\"checkbox\" class=\"LimboniaSortGridCellCheckbox\" name=\"ActiveController[$sAvailableDriver]\" id=\"ActiveController[$sAvailableDriver]\" value=\"1\"$sChecked>");
  $table->addCell($sTypeClass::getGroup() . ' :: ' . $sAvailableName);
  $table->endRow();
}

echo "<form name=\"ManageControllers\" id=\"ManageControllers\" action=\"" . $controller->generateUri('managecontrollers') . "\" method=\"post\">\n";
echo "<input type=\"hidden\" name=\"Column\" id=\"Limbonia_SortGrid_Edit\" value=\"\">\n";
echo $table->toString();
echo "<button type=\"submit\">Update Active Controllers</button>\n";
echo "</form>";