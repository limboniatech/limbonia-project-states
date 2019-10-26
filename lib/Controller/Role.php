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
class Role extends \Limbonia\Controller
{
  use \Limbonia\Traits\ModelController
  {
    \Limbonia\Traits\ModelController::processApiGetModel as originalprocessApiGetModel;
  }

  /**
   * List of sub-menu options
   *
   * @var array
   */
  protected $hSubMenuModels =
  [
    'view' => 'View',
    'edit' => 'Edit',
    'resources' => 'Resources'
  ];

  /**
   * List of actions that are allowed to run
   *
   * @var array
   */
  protected $aAllowedActions = ['search', 'create', 'editdialog', 'editcolumn', 'edit', 'list', 'view', 'resources'];

    /**
   * Generate and return the default model data, filtered by API controls
   *
   * @return array
   * @throws \Exception
   */
  protected function processApiGetModel()
  {
    switch ($this->oRouter->action)
    {
      case 'resources':
        $hResourceList = [];
        $hKeys = $this->oModel->getResourceKeys();

        foreach ($this->oModel->getResourceList() as $oResource)
        {
          $hResourceList[$oResource->id] = $oResource->getAll();
          $hResourceList[$oResource->id]['Level'] = $hKeys[$oResource->id];
        }

        return $hResourceList;
    }

    return $this->originalProcessApiGetModel();
  }

    /**
   * Process the posted resource data and display the result
   */
  protected function prepareViewPostResources()
  {
    try
    {
      $hData = $this->editGetData();

      if (!isset($hData['ResourceKey']))
      {
        throw new \Exception("Resource key list not found");
      }

      $this->oModel->setResourceKeys($hData['ResourceKey']);
      $this->oApp->viewData('success', "This user's resource update has been successful.");
    }
    catch (\Exception $e)
    {
      $this->oApp->viewData('failure', "This user's resource update has failed. <!--" . $e->getMessage() . '-->');
    }

    if (isset($_SESSION['EditData']))
    {
      unset($_SESSION['EditData']);
    }

    $this->oApp->server['request_method'] = 'GET';
    $this->sCurrentAction = 'view';
  }
}