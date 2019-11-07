<?php
if (empty($data))
{
  $sDifferent = $method != 'list' ? ' different' : '';
  echo "Sorry!  Your " . strtolower($controller->getType()) . " " . strtolower($method) . " has no results at this time!<br>\n";
  echo "Try a$sDifferent <a class=\"model\" href=\"" . $controller->generateUri('search') . "\">search</a>?\n";
}
else
{
  $table->makeSortable();
  $table->startHeader();
  $sDelete = $controller->allow('delete') ? '<span class="LimboniaSortGridDelete" onClick="document.getElementById(\'Limbonia_SortGrid_Edit\').name=\'Delete\';document.getElementById(\'EditColumn\').submit();">Delete</span>' : '';
  $table->addCell($sDelete, false);

  foreach ($dataColumns as $column)
  {
    $controller->processSearchGridHeader($table, $column);
  }

  $table->endRow();

  foreach ($data as $model)
  {
    $table->startRow();
    $table->addCell($controller->processSearchGridRowControl($model->getIDColumn(), $model->id));

    foreach ($dataColumns as $column)
    {
      $table->addCell($controller->getColumnValue($model, $column));
    }

    $table->endRow();
  }

  if ($controller->allow('edit'))
  {
    echo "<form name=\"EditColumn\" id=\"EditColumn\" action=\"" . $controller->generateUri('editcolumn') . "\" method=\"post\">\n";
    echo "<input type=\"hidden\" name=\"Column\" id=\"Limbonia_SortGrid_Edit\" value=\"\">\n";
  }

  echo $table->toString();

  if ($controller->allow('edit'))
  {
    echo "</form>\n";
  }
}