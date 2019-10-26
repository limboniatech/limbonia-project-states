<?php
namespace Limbonia\Controller;

/**
 * Limbonia System Controller class
 *
 * Admin controller for handling all the basic system configuration and management
 *
 * @author Lonnie Blansett <lonnie@limbonia.tech>
 * @package Limbonia
 */
class System extends \Limbonia\Controller
{
  /**
   * The admin group that this controller belongs to
   *
   * @var string
   */
  protected static $sGroup = 'Hidden';

  /**
   * List of actions that are allowed to run
   *
   * @var array
   */
  protected $aAllowedActions = ['generatemodelcode', 'managecontrollers', 'config', 'description'];
  /**
   * The default method for this controller
   *
   * @var string
   */
  protected $sDefaultAction = 'description';

  /**
   * The current method being used by this controller
   *
   * @var string
   */
  protected $sCurrentAction = 'description';

  /**
   * List of components that this controller contains along with their descriptions
   *
   * @var array
   */
  protected static $hComponent =
  [
    'description' => 'Explain what the system controller is used for...',
    'managecontrollers' => 'This is the ability to activate and deactivate controllers.',
    'config' => 'The ability to configure the system.'
  ];

  /**
   * List of menu items that this controller should display
   *
   * @var array
   */
  protected $hMenuItems =
  [
    'description' => 'Description',
    'managecontrollers' => 'Manage Controllers',
    'config' => 'Config'
  ];

  /**
   * List of sub-menu options
   *
   * @var array
   */
  protected $hSubMenuItems =
  [
  ];

  /**
   * Deactivate this controller then return a list of types that were deactivated
   *
   * @param array $hActiveController - the active controller list
   * @return array
   * @throws Exception on failure
   */
  public function deactivate(array $hActiveController)
  {
    throw new \Limbonia\Exception('The System controller can not be deactivated');
  }

  /**
   * Perform the base "GET" code then return null on success
   *
   * @return null
   * @throws \Limbonia\Exception
   */
  protected function processApiHead()
  {
    return null;
  }

  /**
   * Perform and return the default "GET" code
   *
   * @return array
   * @throws \Exception
   */
  protected function processApiGet()
  {
    switch ($this->oRouter->action)
    {
      case 'model-controller-base':
        break;

      default:
        throw new \Limbonia\Exception\Web('No action specified for GET System', 0, 400);
    }
  }

  public function generateModelCode($sTable)
  {
    if (empty($sTable))
    {
      throw new \Limbonia\Exception("Table not specified");
    }

    $oDatabase = $this->oApp->getDB();

    if (!$oDatabase->hasTable($sTable))
    {
      throw new \Limbonia\Exception("Table not found: $sTable");
    }


    $hColumns = $oDatabase->getColumns($sTable);
    $sColumns = '';
    $sDefaultData = '';

    foreach ($hColumns as $sName => $hColumn)
    {
      $sDefault = 'null';

      if (isset($hColumn['Default']))
      {
        if (is_null($hColumn['Default']))
        {
          $sDefault = 'null';
        }
        elseif (\Limbonia\Database::columnIsString($hColumn))
        {
          $sDefault = "'" . addslashes($hColumn['Default']) . "'";
        }
        elseif (\Limbonia\Database::columnIsInteger($hColumn))
        {
          $sDefault = (integer)$hColumn['Default'];
        }
        elseif (\Limbonia\Database::columnIsFloat($hColumn))
        {
          $sDefault = (float)$hColumn['Default'];
        }
      }

      $sDefaultData .= "    '$sName' => $sDefault,\n";
      $sColumns .= "\n    '$sName' =>\n    [\n";

      foreach ($hColumn as $sSubName => $sValue)
      {
        if ($sSubName == 'Default')
        {
          if (is_null($sValue))
          {
            $sValue = 'null';
          }
          elseif (\Limbonia\Database::columnIsString($hColumn))
          {
            $sValue = "'$sValue'";
          }
          elseif (\Limbonia\Database::columnIsInteger($hColumn))
          {
            $sValue = (integer)$sValue;
          }
          elseif (\Limbonia\Database::columnIsFloat($hColumn))
          {
            $sValue = (float)$sValue;
          }
        }
        else
        {
          $sValue = "'$sValue'";
        }

        $sColumns .= "      '$sSubName' => $sValue,\n";
      }

      $sColumns = rtrim(rtrim($sColumns), ',') . "
    ],";
    }

    $sColumns = rtrim(rtrim($sColumns), ',');
    $hColumnAlias = \Limbonia\Database::aliasColumns($hColumns);
    $sColumnAlias = '';

    foreach ($hColumnAlias as $sAlias => $sColumn)
    {
      $sColumnAlias .= "    '$sAlias' => '$sColumn',\n";
    }

    $sColumnAlias = rtrim(rtrim($sColumnAlias), ',');
    $sDefaultData = rtrim(rtrim($sDefaultData), ',');
    $sIdColumn = isset($hColumnAlias['id']) ? "'{$hColumnAlias['id']}'" : 'false';

    return "<?php
namespace Limbonia\Model;

/**
 * Limbonia $sTable Model Class
 *
 * Model based wrapper around the User table
 *
 * @author Lonnie Blansett <lonnie@limbonia.tech>
 * @package Limbonia
 */
class $sTable extends \Limbonia\Model
{
  /**
   * The database schema for creating this model's table in the database
   *
   * @var string
   */
  protected static \$sSchema = \"" . $oDatabase->getSchema($sTable) . "\";

  /**
   * The columns for this model's tables
   *
   * @var array
   */
  protected static \$hColumns =
  [
$sColumns
  ];

  /**
   * The aliases for this model's columns
   *
   * @var array
   */
  protected static \$hColumnAlias =
  [
$sColumnAlias
  ];

  /**
   * The default data used for \"blank\" or \"empty\" models
   *
   * @var array
   */
  protected static \$hDefaultData =
  [
$sDefaultData
  ];

  /**
   * This object's data
   *
   * @var array
   */
  protected \$hData =
  [
$sDefaultData
  ];

  /**
   * List of columns that shouldn't be updated after the data has been created
   *
   * @var array
   */
  protected \$aNoUpdate = [$sIdColumn];

  /**
   * The table that this object is referencing
   *
   * @var string
   */
  protected \$sTable = '$sTable';

  /**
   * The name of the \"ID\" column associated with this object's table
   *
   * @var string
   */
  protected \$sIdColumn = $sIdColumn;
}";
  }

  /**
   * Prepare the generatemodelcode view for use
   */
  protected function prepareViewGeneratemodelcode()
  {
    if ($this->oApp->type == 'cli')
    {
      $this->oApp->setDescription('Generate a stub php file for an Model class based on an existing database table');
      $this->oApp->addOption
      ([
        'short' => 't',
        'long' => 'table',
        'desc' => 'The table to base the Model code on',
        'value' => \Limbonia\App\Cli::OPTION_VALUE_REQUIRE
      ]);
    }
  }

  protected function prepareViewPostManagecontrollers()
  {
    $oPost = \Limbonia\Input::singleton('post');
    $aCurrentActiveController = empty($oPost->activecontroller) ? [] : array_keys($oPost->activecontroller);
    $aPriorActiveController = array_keys($this->oApp->activeControllers());
    $aActivate = array_diff($aCurrentActiveController, $aPriorActiveController);
    $aDeactivate = array_diff($aPriorActiveController, $aCurrentActiveController);
    $aError = [];

    foreach ($aActivate as $sController)
    {
      try
      {
        $this->oApp->activateController($sController);
      }
      catch (\Limbonia\Exception $e)
      {
        $aError[] = $e->getMessage();
      }
    }

    foreach ($aDeactivate as $sController)
    {
      try
      {
        $this->oApp->deactivateController($sController);
      }
      catch (\Limbonia\Exception $e)
      {
        $aError[] = $e->getMessage();
      }
    }

    $this->oApp->viewData('error', $aError);
  }
}