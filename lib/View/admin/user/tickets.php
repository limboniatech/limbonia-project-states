<?php
$ticketList = $currentModel->getTickets();

if (count($ticketList) == 0)
{
  echo "Sorry!  This user has no tickets at this time!<br>\n";
}
else
{
  $table = $app->widgetFactory('Table');
  $ticketController = $app->controllerFactory('Ticket');
  $columnList = array_keys($ticketController->getColumns('userTickets'));
  $table->makeSortable();
  $table->startHeader();
  $table->addCell('&nbsp;', false);

  foreach ($columnList as $column)
  {
    $ticketController->processSearchGridHeader($table, $column);
  }

  $table->endRow();

  foreach ($ticketList as $model)
  {
    $oRow = $table->startRow();
    $table->addCell('<a class="model" href="' . $app->generateUri('ticket', $model->id) . '">View</a>');

    foreach ($columnList as $column)
    {
      $table->addCell($ticketController->getColumnValue($model, $column));
    }

    $table->endRow();
  }

  echo $table->toString();
}