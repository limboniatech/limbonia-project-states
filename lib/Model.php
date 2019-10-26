<?php
namespace Limbonia;

/**
 * Limbonia Model Class
 *
 * This is a wrapper around the around a row of model data that allows access to
 * the data
 *
 * @author Lonnie Blansett <lonnie@limbonia.tech>
 * @package Limbonia
 */
class Model extends Item
{
  use \Limbonia\Traits\HasApp;
  /**
   * Get the specified data
   *
   * @param string $sName
   * @return mixed
   */
  public function __get($sName)
  {
    $xGet = parent::__get($sName);

    //if the returned data is a Model and we have a valid App
    if ($xGet instanceof Model && $this->oApp instanceof App)
    {
      //then set the App in the Model
      $xGet->setApp($this->oApp);
    }

    return $xGet;
  }
}