<?php
namespace Limbonia\Controller;

/**
 * Limbonia Role Controller class
 *
 * Admin controller for handling groups
 *
 * @author Lonnie Blansett <lonnie@limbonia.tech>
 * @package Limbonia
 */
class ZipCode extends \Limbonia\Controller
{
  use \Limbonia\Traits\Controller\HasModel;

  /**
   * Activate this controller and any required dependencies then return a list of types that were activated
   *
   * @param array $hActiveController - the active controller list
   * @return array
   * @throws Exception on failure
   */
  public function activate(array $hActiveController)
  {
    $oState = $this->oApp->modelFactory('states');
    $oState->setup();
    return parent::activate($hActiveController);
  }

  /**
   * Deactivate this controller then return a list of types that were deactivated
   *
   * @param array $hActiveController - the active controller list
   * @return array
   * @throws Exception on failure
   */
  public function deactivate(array $hActiveController)
  {
    throw new \Limbonia\Exception('The ZipCode controller can not be deactivated');
  }
}