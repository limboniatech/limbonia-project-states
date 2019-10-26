<?php
namespace Limbonia\Traits;

trait ModelReport
{
  /**
   * Generate and return the result data for the current configuration of this report
   *
   *
   * @return \Limbonia\Interfaces\Result
   * @throws \Limbonia\Exception
   */
  protected function generateResult(): \Limbonia\Interfaces\Result
  {
    $sModelDriver = \Limbonia\Model::driver($this->getType());

    if (empty($sModelDriver))
    {
      throw new \Limbonia\Exception("Driver for type ($this->sType) not found");
    }

    $oModel = $this->oApp->modelFactory($this->sType);
    $sTable = $oModel->getTable();
    $oDatabase = $this->oApp->getDB();
    $aFields = empty($this->aFields) ? array_keys($this->hHeaders) : array_intersect($oDatabase->verifyColumns($sTable, array_merge(['id'], $this->aFields)), array_keys($this->hHeaders));

    //default order is according to the ID column of this model
    $aOrder = empty($this->aOrder) ? ['id'] : $this->aOrder;
    return $oDatabase->query($oDatabase->makeSearchQuery($sTable, $aFields, $this->hSearch, $aOrder));
  }
}
