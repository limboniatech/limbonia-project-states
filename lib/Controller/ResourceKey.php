<?php
namespace Limbonia\Controller;

/**
 * Limbonia Resource Controller class
 *
 * Admin controller for handling site resource keys
 *
 * @author Lonnie Blansett <lonnie@limbonia.tech>
 * @package Limbonia
 */
class ResourceKey extends \Limbonia\Controller
{
  use \Limbonia\Traits\ModelController;

  /**
   * List of menu items that this controller should display
   *
   * @var array
   */
  protected $hMenuItems =
  [
    'list' => 'List',
    'create' => 'Create'
  ];
}