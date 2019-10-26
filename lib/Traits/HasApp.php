<?php
namespace Limbonia\Traits;

/**
 * Limbonia HasApp Trait
 *
 * This trait allows an inheriting class to have a app
 *
 * @author Lonnie Blansett <lonnie@limbonia.tech>
 * @package Limbonia
 */
trait HasApp
{
  /**
   * The app for this object
   *
   * @var \Limbonia\App
   */
  protected $oApp = null;

  /**
   * Set this object's app
   *
   * @param \Limbonia\App $oApp
   */
  public function setApp(\Limbonia\App $oApp)
  {
    $this->oApp = $oApp;
  }

  /**
   * Return this object's app
   *
   * @return \Limbonia\App
   */
  public function getApp(): \Limbonia\App
  {
    if (is_null($this->oApp))
    {
      return \Limbonia\App::getDefault();
    }

    return $this->oApp;
  }
}
