<?php
namespace Limbonia;

/**
 * Limbonia base App Class
 *
 * The app
 *
 * @author Lonnie Blansett <lonnie@limbonia.tech>
 * @package Limbonia
 */
abstract class App
{
  const SETTINGS_NAME_ACTIVE_controllerS = 'ActiveControllers';

  /**
   * The config that comes from external sources
   *
   * @var array
   */
  protected static $hAutoConfig = [];

    /**
   * The current default App
   *
   * @var \Limbonia\App
   */
  protected static $oDefaultApp = null;

  /**
   * @var \DateTime $oBuildDate -
   */
  protected static $oBuildDate = null;

  /**
   * @var string $sBuildVersion -
   */
  protected static $sBuildVersion = '0.0.0';

  /**
   * The format for timestamps
   *
   * @var string
   */
  protected static $sTimeStampFormat = "G:i:s M j Y";

  /**
   * List of Limbonia lib directories
   *
   * @var array
   */
  protected static $aLibList = [__DIR__];

  /**
   * List of Limbonia lib directories
   *
   * @var array
   */
  protected static $aViewDir = [__DIR__ . '/View'];

  /**
   * The list of input types that are allowed to be auto generated
   *
   * @var array
   */
  protected static $aAutoInput = ['get', 'post', 'server'];

  /**
   * List of currently instantiated controllers
   *
   * @var array
   */
  protected static $hControllerList = [];

  /**
   * The list of currently available controllers
   *
   * @var array
   */
  protected static $hAvailableController = null;

  /**
   * The list of currently active controllers
   *
   * @var array
   */
  protected static $hActiveController = null;

  /**
   * The list of controllers allowed for the current user
   *
   * @var array
   */
  protected static $hAllowedController = null;

  /**
   * List of app types that are based on the web app
   *
   * @var array
   */
  const WEB_TYPES =
  [
    'admin',
    'ajax',
    'api',
    'web'
  ];

  /**
   * All the data that will be used by the views
   *
   * @var array
   */
  protected $hViewData = [];

  /**
   * @var \Limbonia\Domain - The default domain for this app instance
   */
  protected $oDomain = null;

  /**
   * List of database objects
   *
   * @var array
   */
  protected $hDatabaseList = [];

  /**
   * List of database configuration settings
   *
   * @var array
   */
  protected $hDatabaseConfig = [];

  /**
   * List of configured directories
   *
   * @var array
   */
  protected $hDirectories =
  [
    'root' => '',
    'libs' => []
  ];

  /**
   * List of default Model type names and what they should default to
   *
   * @var array
   */
  protected $hModelTypeDefaults = [];

  protected $aDefaultActiveControllers =
  [
    'system',
    'zipcode',
    'resourcekey',
    'resourcelock',
    'role',
    'user',
    'auth',
    'profile'
  ];
  /**
   * List of configuration data
   *
   * @var array
   */
  protected $hConfig = [];

  /**
   * The logged in user
   *
   * @var \Limbonia\Model\User
   */
  protected $oUser = null;

  /**
   * The type of app that has been instantiated
   *
   * @var string
   */
  protected $sType = '';

  /**
   * Is this app running in debug mode?
   *
   * @var boolean
   */
  protected $bDebug = false;

  /**
   * Hash of rules used to ensure password strength
   *
   * @var array
   */
  protected $hPasswordRules =
  [
    'empty' => false,
    'charactermin' => 8,
    'requirespecial' => true,
    'requirenumber' => true,
    'charactermax' => 255
  ];

  /**
   * This App's router, if there is one
   *
   * @var \Limbonia\Router
   */
  protected $oRouter = null;

  /**
   * Generate the build data so it can be used in other places
   */
  protected static function generateBuildData()
  {
    if (\is_null(self::$oBuildDate))
    {
      $sVersionFile = __DIR__ . DIRECTORY_SEPARATOR . 'version';

      if (is_file($sVersionFile))
      {
        self::$oBuildDate = new \DateTime('@' . filemtime($sVersionFile));
        self::$sBuildVersion = trim(file_get_contents($sVersionFile));
      }
    }
  }

  /**
   * Is the CLI running?
   *
   * @return boolean
   */
  public static function isCLI()
  {
    return preg_match("/cli/i", PHP_SAPI);
  }

  /**
   * Is this running from the web?
   *
   * @return boolean
   */
  public static function isWeb()
  {
    return !self::isCLI();
  }

  /**
   * return the correct EOL for the current environment.
   *
   * @return string
   */
  public static function eol()
  {
    return self::isCLI() ? "\n" : "<br>\n";
  }

  /**
   * Return the build date of the current release of Limbonia.
   *
   * @param string $sFormat (optional) - Override the default format with this one, if it's is used
   */
  public static function buildDate($sFormat = '')
  {
    self::generateBuildData();
    return self::$oBuildDate->format(empty($sFormat) ? 'r' : $sFormat);
  }

  /**
   * Set all apps to use the specified format as the default format for timestamps
   *
   * @param string $sNewFormat
   */
  public static function setTimeStampFormat($sNewFormat = NULL)
  {
    self::$sTimeStampFormat = empty($sNewFormat) ? 'r' : $sNewFormat;
  }

  /**
   * Format and return the specified UNIX timestamp using the default format
   *
   * @param integer $iTimeStamp
   * @param string $sFormat (optional) - Override the default format with this one, if it's is used
   * @return string
   */
  public static function formatTime($iTimeStamp, $sFormat = '')
  {
    $oTime = new \DateTime('@' . (integer)$iTimeStamp);
    $sFormat = empty($sFormat) ? self::$sTimeStampFormat : $sFormat;
    return $oTime->format($sFormat);
  }

  /**
   * Generate and return the current time in the default format
   *
   * @param string $sFormat (optional) - Override the default format with this one, if it's is used
   * @return string
   */
  public static function timeStamp($sFormat = NULL)
  {
    return self::formatTime(time(), $sFormat);
  }

  /**
   * Return the version number of the current release of Limbonia.
   *
   * @return string
   */
  public static function version()
  {
    self::generateBuildData();
    return self::$sBuildVersion;
  }

  /**
   * Set the default app for this PHP instance
   *
   * @param App $oApp
   */
  public static function setDefault(self $oApp)
  {
    self::$oDefaultApp = $oApp;
  }

  /**
   * Return the default app for this PHP instance
   *
   * @return App
   */
  public static function getDefault()
  {
    return self::$oDefaultApp;
  }

  /**
   * Flatten the specified variable into a string and return it...
   *
   * @param mixed $xData
   * @return string
   */
  public static function flatten($xData)
  {
    return var_dump($xData, true);
  }

  /**
   * PSR-4 compatible autoload method
   *
   * @param string $sClassName
   */
  public static function autoload($sClassName)
  {
    $sClassType = preg_match("#^" . __NAMESPACE__ . "\\\?(.+)#", $sClassName, $aMatch) ? $aMatch[1] : $sClassName;
    $sClassPath = preg_replace("#[_\\\]#", DIRECTORY_SEPARATOR, $sClassType);

    foreach (self::getLibs() as $sLibDir)
    {
      $sClassFile = $sLibDir . DIRECTORY_SEPARATOR . $sClassPath . '.php';

      if (is_file($sClassFile))
      {
        require $sClassFile;
        break;
      }
    }
  }

  /**
   * Register the PSR-4 autoloader
   */
  public static function registerAutoloader()
  {
    set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__);
    spl_autoload_register([__NAMESPACE__ . '\\App', 'autoload'], false);
  }

  /**
   * Add a new Limbonia library to the current list
   *
   * @param string $sLibDir - The root directory to the Limbonia library to add
   */
  public static function addLib($sLibDir)
  {
    if (is_dir($sLibDir) && !in_array($sLibDir, self::$aLibList))
    {
      array_unshift(self::$aLibList, $sLibDir);
      array_unshift(self::$aViewDir, "$sLibDir/View");
    }
  }

  /**
   * Return the list of Limbonia libraries
   *
   * @return array
   */
  public static function getLibs()
  {
    return self::$aLibList;
  }

  /**
   * Return the list of view directories
   *
   * @return string
   */
  public static function viewDirs()
  {
    return self::$aViewDir;
  }

  /**
   * Find and return the home directory of the current user
   *
   * @return string
   */
  public static function getHomeDir()
  {
    if (isset($_SERVER['HOME']))
    {
      return $_SERVER['HOME'];
    }

    $sHome = getenv('HOME');

    if (!empty($sHome))
    {
      return $sHome;
    }

    $hUser = posix_getpwuid(posix_getuid());
    return $hUser['dir'];
  }

  /**
   * Merge two arrays recursively and return it
   *
   * @param array $hOriginal
   * @param array $hOverride
   * @return array
   */
  public static function mergeArray(array $hOriginal, array $hOverride)
  {
    $hMerge = $hOriginal;

    foreach ($hOverride as $sKey => $xValue)
    {
      if (isset($hOriginal[$sKey]))
      {
        if (is_array($xValue) && is_array($hOriginal[$sKey]))
        {
          $hMerge[$sKey] = self::mergeArray($hOriginal[$sKey], $xValue);
        }
        else
        {
          $hMerge[$sKey] = $hOverride[$sKey];
        }
      }
      else
      {
        $hMerge[$sKey] = $xValue;
      }
    }

    return $hMerge;
  }

  /**
   * Add a new hash to the default config
   *
   * @param array $hNewConfig
   */
  public static function addAutoConfig(array $hNewConfig = [])
  {
    self::$hAutoConfig = self::mergeArray(self::$hAutoConfig, $hNewConfig);
  }

  /**
   * Generate and return a valid, configured app
   *
   * @param array $hConfig
   * @return \Limbonia\App
   * @throws \Exception
   */
  public static function factory(array $hConfig = [])
  {
    if (is_file('/etc/limbonia/config.php'))
    {
      require_once '/etc/limbonia/config.php';
    }

    $sHome = \Limbonia\App::getHomeDir();
    $sConfigFile = "$sHome/.limbonia/config.php";

    if (is_file($sConfigFile))
    {
      require_once $sConfigFile;
    }

    $hConfig = self::mergeArray(self::$hAutoConfig, $hConfig);
    $hLowerConfig = \array_change_key_case($hConfig, CASE_LOWER);
    $sAppType = null;

    if (isset($hLowerConfig['app_type']))
    {
      $sAppType = strtolower($hLowerConfig['app_type']);
      unset($hLowerConfig['app_type']);
    }

    if (self::isCLI() || $sAppType == 'cli')
    {
      return new App\Cli($hLowerConfig);
    }

    if (!in_array($sAppType, self::WEB_TYPES))
    {
      $oServer = Input::singleton('server');
      $sBaseUrl = rtrim(dirname($oServer['php_self']), '/') . '/';
      $sRawPath = rtrim(preg_replace("#\?.*#", '', preg_replace("#^" . $sBaseUrl . "#",  '', $oServer['request_uri'])), '/');
      $aCall = explode('/', strtolower($sRawPath));
      $sAppType = isset($aCall[0]) && in_array($aCall[0], self::WEB_TYPES) ? $aCall[0] : 'web';
    }

    $sWebAppClass = __CLASS__ . '\\' . ucfirst($sAppType);
    return new $sWebAppClass($hLowerConfig);
  }

  /**
   * The app constructor
   *
   * NOTE: This constructor should only be used by the factory and *never* directly
   *
   * @param array $hConfig - A hash of configuration data
   */
  protected function __construct(array $hConfig = [])
  {
    if (isset($hConfig['debug']))
    {
      $this->bDebug = (boolean)$hConfig['debug'];
    }

    $this->sType = strtolower(str_replace(__CLASS__ . "\\", '', get_class($this)));

    if (isset($hConfig['domaindirview']))
    {
      Domain::setDirView($hConfig['domaindirview']);
      unset($hConfig['domaindirview']);
    }

    if (isset($hConfig['domain']))
    {
      if ($hConfig['domain'] instanceof \Limbonia\Domain)
      {
        $this->oDomain = $hConfig['domain'];
      }
      elseif (is_string($hConfig['domain']))
      {
        $this->oDomain = \Limbonia\Domain::factory($hConfig['domain']);
      }

      unset($hConfig['domain']);
    }

    $this->hConfig['baseuri'] = $this->oDomain ? $this->oDomain->uri : '';
    $this->hDirectories['root'] = \dirname(__DIR__);

    if (isset($hConfig['directories']))
    {
      foreach ($hConfig['directories'] as $sName => $sDir)
      {
        $this->hDirectories[\strtolower($sName)] = $sDir;
      }

      unset($hConfig['directories']);
    }

    $sViewDir = $this->getDir('view');

    if (is_readable($sViewDir) && !in_array($sViewDir, self::$aViewDir))
    {
      array_unshift(self::$aViewDir, $sViewDir);
    }

    $sTimeZone = 'UTC';

    if (isset($hConfig['timezone']))
    {
      $sTimeZone = $hConfig['timezone'];
      unset($hConfig['timezone']);
    }

    date_default_timezone_set($sTimeZone);

    if (isset($hConfig['database']) && count($hConfig['database']) > 0)
    {
      foreach ($hConfig['database'] as $sName => $hDatabase)
      {
        $this->hDatabaseConfig[\strtolower($sName)] = array_change_key_case($hDatabase, CASE_LOWER);
      }

      unset($hConfig['database']);
    }

    if (isset($hConfig['modeltypedefaults']))
    {
      $this->hModelTypeDefaults = $hConfig['modeltypedefaults'];
      unset($hConfig['modeltypedefaults']);
    }

    if (isset($hConfig['defaultactivecontrollers']))
    {
      $this->aDefaultActiveControllers = $hConfig['defaultactivecontrollers'];
      unset($hConfig['defaultactivecontrollers']);
    }

    if (isset($hConfig['passwordrules']))
    {
      $this->hPasswordRules = array_merge($this->hPasswordRules, array_change_key_case($hConfig['passwordrules'], CASE_LOWER));
      unset($hConfig['passwordrules']);
    }

    $this->hConfig = array_merge($this->hConfig, $hConfig);

    if (\is_null(self::$oDefaultApp))
    {
      self::setDefault($this);
    }
  }

  /**
   * Magic method used to set the specified property to the specified value
   *
   * @note Settings should not be changed so this method does nothing...
   *
   * @param string $sName
   * @param mixed $xValue
   */
  public function __set($sName, $xValue)
  {
    //don't allow public setting of anything
  }

  /**
   * Magic method used to generate and return the specified property
   *
   * @param string $sName
   * @return mixed
   */
  public function __get($sName)
  {
    $sLowerName = strtolower($sName);

    if (in_array($sLowerName, self::$aAutoInput))
    {
      return \Limbonia\Input::singleton($sLowerName);
    }

    if ($sLowerName == 'domain')
    {
      return $this->oDomain;
    }

    if ($sLowerName == 'type')
    {
      return $this->sType;
    }

    if ($sLowerName == 'debug')
    {
      return $this->bDebug;
    }

    if (preg_match("#^(.+?)dir$#", $sLowerName, $aMatch))
    {
      return $this->getDir($aMatch[1]);
    }

    if (isset($this->hConfig[$sLowerName]))
    {
      return $this->hConfig[$sLowerName];
    }
  }

  /**
   * Magic method used to determine if the specified property is set
   *
   * @param string $sName
   * @return boolean
   */
  public function __isset($sName)
  {
    $sLowerName = strtolower($sName);

    if (in_array($sLowerName, self::$aAutoInput))
    {
      return true;
    }

    if ($sLowerName === 'domain')
    {
      return !empty($this->oDomain);
    }

    if ($sLowerName == 'type' || $sLowerName == 'debug')
    {
      return true;
    }

    if (preg_match("#^(.+?)dir$#", $sLowerName))
    {
      return true;
    }

    return isset($this->hConfig[$sLowerName]);
  }

  /**
   * Magic method used to remove the specified property
   *
   * @note Settings should not be unset so this method does nothing...
   *
   * @param string $sName
   */
  public function __unset($sName)
  {
    //don't allow public unsetting of anything
  }

  /**
   * Run basic system setup
   */
  public function setup()
  {
    $oDatabase = $this->getDB();

    //create the settings table
    echo "Initialize Settings: ";
    $oDatabase->createTable('Settings', "Type VARCHAR(255) NOT NULL,
Data TEXT NULL,
PRIMARY KEY(Type)");
    echo "complete" . static::eol();

    //activate the default controllers
    echo "Initialize Default Controllers:" . static::eol();

    foreach ($this->aDefaultActiveControllers as $sController)
    {
      try
      {
        echo "\t$sController: ";
        $this->activateController($sController);
        echo "complete" . static::eol();
      }
      catch (Exception $e)
      {
        echo $e->getMessage() . static::eol();
      }
    }
  }

  /**
   * Generate and return a database object based on the specified database config section
   *
   * @param string $sSection (optional)
   * @throws \Limbonia\Exception\Database
   * @return \Limbonia\Database
   */
  public function getDB($sSection = 'default')
  {
    if (empty($this->hDatabaseConfig))
    {
      throw new Exception\Database("Database not configured");
    }

    if (empty($sSection) || !isset($this->hDatabaseConfig[$sSection]))
    {
      $sSection = 'default';
    }

    if (!isset($this->hDatabaseList[$sSection]))
    {
      if (!isset($this->hDatabaseConfig[$sSection]))
      {
        throw new Exception\Database("Database default not configured");
      }

      $this->hDatabaseList[$sSection] = Database::factory($this->hDatabaseConfig[$sSection], $this);
    }

    return $this->hDatabaseList[$sSection];
  }

  /**
   * Return the Domain object that is associated with this App, if there is one
   *
   * @return \Limbonia\Domain
   */
  public function getDomain()
  {
    return $this->oDomain;
  }

  /**
   * Get the specified directory via several different means
   *
   * @param string $sDirName
   * @return string
   */
  public function getDir($sDirName)
  {
    $sDirName = \strtolower($sDirName);

    //Check to see if it's specifiacally configured
    if (isset($this->hDirectories[$sDirName]))
    {
      return $this->hDirectories[$sDirName];
    }

    //Check to see if it exists in the configured "Custom" directory
    if (isset($this->hDirectories['custom']))
    {
      if (is_dir($this->hDirectories['custom'] . DIRECTORY_SEPARATOR . $sDirName))
      {
        return $this->hDirectories['custom'] . DIRECTORY_SEPARATOR . $sDirName;
      }
    }

    //Check to see if it exists relative to the current path
    $sTemp = realpath($sDirName);

    if ($sTemp)
    {
      return $sTemp;
    }

    //is this the temp directory
    if ($sDirName == 'temp')
    {
      return '/tmp';
    }

    //if all else fails then use the current directory
    return '';
  }

  /**
   * Generate and return the URI for the specified parameters
   *
   * @param string ...$aParam (optional)
   * @return string
   */
  public function generateUri(string ...$aParam): string
  {
    $aUri = array_merge([$this->baseUri], $aParam);
    return strtolower(implode('/', $aUri));
  }

  /**
   * Save the specified settings for the specified type to the database
   *
   * @param string $sType
   * @param array $hSettings
   * @return boolean - True on success or false on failure
   */
  public function saveSettings($sType, array $hSettings = [])
  {
    $oStatement = $this->getDB()->prepare('INSERT INTO Settings (Type, Data) VALUES (:Type, :Data) ON DUPLICATE KEY UPDATE Data = :Data');
    return $oStatement->execute
    ([
      ':Type' => $sType,
      ':Data' => addslashes(serialize($hSettings))
    ]);
  }

  /**
   * Return settings of the specified type
   *
   * @param string $sType
   * @return array
   * @throws \Exception
   */
  public function getSettings($sType)
  {
    $oStatement = $this->getDB()->prepare('SELECT Data FROM Settings WHERE Type = :Type LIMIT 1');
    $oStatement->bindParam(':Type', $sType);

    if (!$oStatement->execute())
    {
      $aError = $oStatement->errorInfo();
      throw new \Exception("Failed to get settings for $sType: " . $aError[2]);
    }

    $sSettings = $oStatement->fetchColumn();
    return empty($sSettings) ? [] : unserialize(stripslashes($sSettings));
  }

  /**
   * Return the default for the specified type
   *
   * @param string $sType
   * @return striing
   */
  public function defaultModelType($sType)
  {
    $sLowerType = strtolower($sType);
    return isset($this->hModelTypeDefaults[$sLowerType]) ? $this->hModelTypeDefaults[$sLowerType] : $sType;
  }

  /**
   * Add the specified data to the view under the specified name
   *
   * @param string $sName
   * @param mixed $xValue
   */
  public function viewData($sName, $xValue)
  {
    $this->hViewData[$sName] = $xValue;
  }

  /**
   * Render and return specified view
   *
   * @param string $sViewName
   * @return string The rendered view
   */
  public function viewRender($sViewName)
  {
    $sViewFile = $this->viewFile($sViewName);

    if (empty($sViewFile))
    {
      return '';
    }

    ob_start();
    $this->viewInclude($sViewFile);
    return ob_get_clean();
  }

  /**
   * Return the full file path of the specified view, if it exists
   *
   * @param string $sViewName
   * @return string
   */
  public function viewFile($sViewName)
  {
    if (empty($sViewName))
    {
      return '';
    }

    if (is_readable($sViewName) && !is_dir($sViewName))
    {
      return $sViewName;
    }

    foreach (self::viewDirs() as $sLib)
    {
      $sFilePath = $sLib . '/' . $this->sType . '/' .$sViewName;

      if (is_readable($sFilePath) && !is_dir($sFilePath))
      {
        return $sFilePath;
      }

      if (is_readable("$sFilePath.php"))
      {
        return "$sFilePath.php";
      }

      if (is_readable("$sFilePath.html"))
      {
        return "$sFilePath.html";
      }
    }

    return '';
  }

  /**
   * Find then include the specified view if it's found
   *
   * @param srtring $sViewName
   */
  protected function viewInclude($sViewName)
  {
    $sViewFile = $this->viewFile($sViewName);

    if ($sViewFile)
    {
      extract($this->hViewData);
      include $sViewFile;
    }
  }

  /**
   * Generate and return an empty model object based on the specified table.
   *
   * @param string $sType
   * @return \Limbonia\Model
   */
  public function modelFactory($sType): \Limbonia\Model
  {
    $oModel = Model::factory($this->defaultModelType($sType), $this->getDB());
    $oModel->setApp($this);
    return $oModel;
  }

  /**
   * Generate and return an model object filled with data from the specified table id
   *
   * @param string $sType
   * @param integer $iModel
   * @throws \Limbonia\Exception\Database
   * @return \Limbonia\Model
   */
  public function modelFromId($sType, $iModel): \Limbonia\Model
  {
    $oModel = Model::fromId($this->defaultModelType($sType), $iModel, $this->getDB());
    $oModel->setApp($this);
    return $oModel;
  }

  /**
   * Generate and return an model object filled with data from the specified array
   *
   * @param string $sType
   * @param array $hModel
   * @return \Limbonia\Model
   * @throws \Limbonia\Exception\Object
   */
  public function modelFromArray($sType, $hModel): \Limbonia\Model
  {
    $oModel = Model::fromArray($this->defaultModelType($sType), $hModel, $this->getDB());
    $oModel->setApp($this);
    return $oModel;
  }

  /**
   * Generate an model list based on the specified type and SQL query
   *
   * @param string $sType
   * @param string $sQuery
   * @return \Limbonia\ModelList
   */
  public function modelList($sType, $sQuery): \Limbonia\ModelList
  {
    $oList = Model::getList($this->defaultModelType($sType), $sQuery, $this->getDB());
    $oList->setApp($this);
    return $oList;
  }

  /**
   * Generate an model list based on the specified type and search criteria
   *
   * @param string $sType
   * @param array $hWhere
   * @param mixed $xOrder
   * @return \Limbonia\ModelList
   */
  public function modelSearch($sType, $hWhere = null, $xOrder = null)
  {
    $oList = Model::search($this->defaultModelType($sType), $hWhere, $xOrder, $this->getDB());
    $oList->setApp($this);
    return $oList;
  }

  /**
   * Generate and return an empty model object based on the specified table.
   *
   * @param string $sType
   * @param string $sName (optional) - The name to give the widget when it is instantiated
   * @return \Limbonia\Widget - The requested \Limbonia\Widget on success, otherwise FALSE.
   */
  public function widgetFactory($sType, $sName = null)
  {
    return Widget::factory($sType, $sName, $this);
  }

  /**
   * Generate and return the controller of the specified type
   *
   * @param string $sType
   * @return \Limbonia\Controller
   */
  public function controllerFactory($sType)
  {
    $sDriver = Controller::driver($sType);

    if (!isset(self::$hControllerList[$sDriver]))
    {
      self::$hControllerList[$sDriver] = Controller::factory($sType, $this);
    }

    return self::$hControllerList[$sDriver];
  }

  /**
   * Generate and return a Report object of the specified type
   *
   * @param string $sType
   * @param array $hParam (optional)
   * @return \Limbonia\Report
   */
  public function reportFactory($sType, array $hParam = []): \Limbonia\Report
  {
    return \Limbonia\Report::factory($sType, $hParam, $this);
  }

  /**
   * Generate a report, run it then return the result
   *
   * @param string $sType The type of report to get a result from
   * @param array $hParam (optional) List of report parameters to set before running the report
   * @return \Limbonia\Interfaces\Result
   * @throws \Limbonia\Exception\Object
   */
  public function reportResultFactory($sType, array $hParam = [])
  {
    return \Limbonia\Report::resultFactory($sType, $hParam, $this);
  }

  /**
   * Return the default router
   *
   * @return \Limbonia\Router
   */
  public function getRouter()
  {
    if (empty($this->oRouter))
    {
      $this->oRouter = \Limbonia\Router::singleton();
    }

    return $this->oRouter;
  }

  /**
   * Return the list of all available controllers
   *
   * @return array
   */
  public function availableControllers()
  {
    if (is_null(self::$hAvailableController))
    {
      $hDriverList = \Limbonia\Controller::driverList();
      $aBlackList = $this->controllerBlackList ?? [];

      foreach ($hDriverList as $sDriver)
      {
        if (in_array($sDriver, $aBlackList))
        {
          continue;
        }

        self::$hAvailableController[strtolower($sDriver)] = $sDriver;
      }

      ksort(self::$hAvailableController);
      reset(self::$hAvailableController);
    }

    return self::$hAvailableController;
  }

  /**
   * Return the list of all active controllers
   *
   * @return array
   */
  public function activeControllers()
  {
    if (is_null(self::$hActiveController))
    {
      self::$hActiveController = $this->getSettings(self::SETTINGS_NAME_ACTIVE_controllerS);
    }

    return self::$hActiveController;
  }

  /**
   * Activate the specified controller
   *
   * @param string $sController the name of the controller to activate
   * @throws Exception
   */
  public function activateController($sController)
  {
    if (empty($sController))
    {
      throw new Exception("Controller driver not specified");
    }

    $sDriver = Controller::driver($sController);

    if (empty($sDriver))
    {
      throw new Exception("Controller driver not found: $sController");
    }

    $sLowerDriver = strtolower($sDriver);
    $hActiveController = $this->activeControllers();

    //if this controller type is already one of the active controllers
    if (isset($hActiveController[$sLowerDriver]))
    {
      //then fail
      throw new Exception("That controller is already active");
    }

    $oController = $this->controllerFactory($sDriver);

    foreach ($oController->activate($hActiveController) as $sActivedDriver)
    {
      self::$hActiveController[strtolower($sActivedDriver)] = $sActivedDriver;
    }

    if (!$this->saveSettings(self::SETTINGS_NAME_ACTIVE_controllerS, self::$hActiveController))
    {
      throw new Exception("Failed to save new active controller list");
    }
  }

  /**
   * Deactivate the specified controller
   *
   * @param string $sController the name of the controller to deactivate
   * @throws Exception
   */
  public function deactivateController($sController)
  {
    if (empty($sController))
    {
      throw new Exception("Controller driver not specified");
    }

    $sDriver = Controller::driver($sController);

    if (empty($sDriver))
    {
      throw new Exception("Controller driver not found: $sController");
    }

    $sLowerDriver = strtolower($sDriver);
    $hActiveController = $this->activeControllers();

    //if this controller type is not one of the active controllers
    if (!isset($hActiveController[$sLowerDriver]))
    {
      //then fail
      throw new Exception("The $sDriver controller is already inactive");
    }

    $oController = $this->controllerFactory($sDriver);

    foreach ($oController->deactivate($hActiveController) as $sDeactivedDriver)
    {
      unset(self::$hActiveController[strtolower($sDeactivedDriver)]);
    }

    if (!$this->saveSettings(self::SETTINGS_NAME_ACTIVE_controllerS, self::$hActiveController))
    {
      throw new Exception("Failed to save new active controller list");
    }
  }

  /**
   * The list of all controllers the current user is allowed to access
   *
   * @return array
   */
  public function allowedControllers()
  {
    if (is_null(self::$hAllowedController))
    {
      $hDriverList = \Limbonia\Controller::driverList();

      foreach ($hDriverList as $sDriver)
      {
        if (!$this->oUser->hasResource($sDriver))
        {
          continue;
        }

        self::$hAllowedController[strtolower($sDriver)] = $sDriver;
      }

      ksort(self::$hAllowedController);
      reset(self::$hAllowedController);
    }

    return self::$hAllowedController;
  }

  /**
   * Make sure the specified password follows all the current guidelines
   *
   * @todo Create method for adding / controlling the password guidelines with config and scripting options
   *
   * @param string $sPassword
   * @throws \Exception
   */
  public function validatePassword($sPassword)
  {
    $aPasswordProblems = [];

    if (!empty($this->hPasswordRules))
    {
      foreach ($this->hPasswordRules as $sRule => $xValue)
      {
        switch ($sRule)
        {
          case 'empty':
            if (!(boolean)$xValue)
            {
              if (empty($sPassword))
              {
                $aPasswordProblems[] = 'Password may not be empty';
              }
            }
            break;

          case 'requirenumber':
            if ((boolean)$xValue)
            {
              if (!preg_match("/[0-9]/", $sPassword))
              {
                $aPasswordProblems[] = 'Password requires at least 1 number';
              }
            }

            break;

          case 'requirespecial':
            if ((boolean)$xValue)
            {
              if (!preg_match("/[\!\@\#\$\%\^\&\*\(\)\-\=\_\+\[\]\\\`\{\}\|\~\;\'\:\"\,\.\/\<\>\?]/", $sPassword))
              {
                $aPasswordProblems[] = 'Password requires at least 1 special character';
              }
            }
            break;

          case 'requireupper':
            if ((boolean)$xValue)
            {
              if (!preg_match("/[A-Z]/", $sPassword))
              {
                $aPasswordProblems[] = 'Password requires at least 1 uppercase character';
              }
            }
            break;

          case 'requirelower':
            if ((boolean)$xValue)
            {
              if (!preg_match("/[a-z]/", $sPassword))
              {
                $aPasswordProblems[] = 'Password requires at least 1 lowercase character';
              }
            }
            break;

          case 'charactermin':
            $iMin = (int)$xValue;
            $iCharCount = strlen($sPassword);

            if ($iCharCount < $iMin)
            {
              $aPasswordProblems[] = "Password is only $iCharCount characters long but must be at least $iMin characters long";
            }

            break;

          case 'charactermax':
            $iMax = (int)$xValue;
            $iCharCount = strlen($sPassword);

            if (isset($this->hPasswordRules['charactermin']))
            {
              $iMin = (int)$this->hPasswordRules['charactermin'];

              if ($iMax < $iMin)
              {
                $iMax = $iMin;
              }
            }

            $oUser = $this->modelFactory('user');
            $hPasswordColumn = $oUser->getColumn('password');

            if (preg_match("/varchar\((\d+)\)/", $hPasswordColumn['Type'], $aMatch))
            {
              $iDatabaseMax = (int)$aMatch[1];

              if ($iMax > $iDatabaseMax)
              {
                $iMax = $iDatabaseMax;
              }
            }

            if ($iCharCount > $iMax)
            {
              $aPasswordProblems[] = "Password is $iCharCount characters long but can not be more than $iMax characters long";
            }

            break;
        }
      }
    }

    if (!empty($aPasswordProblems))
    {
      throw new Exception('The password contains the following problems: ' . static::eol() . implode(static::eol(), $aPasswordProblems) . static::eol());
    }
  }

  /**
   * Return the user represented by the specified email, if there is one
   *
   * @param string $sEmail
   * @return \Limbonia\Model\User
   */
  public function userByEmail($sEmail)
  {
    $oUser = \Limbonia\Model\User::getByEmail($sEmail, $this->getDB());
    $oUser->setApp($this);
    return $oUser;
  }

  /**
   * Return a default admin User object
   *
   * @return \Limbonia\Model\User
   */
  public function userAdmin()
  {
    $oUser = \Limbonia\Model\User::getAdmin();
    $oUser->setApp($this);
    return $oUser;
  }

  /**
   * Return the currently logged in user
   *
   * @return \Limbonia\Model\User
   */
  public function user()
  {
    return $this->oUser;
  }

  /**
   * Generate and return the current user
   *
   * @return \Limbonia\Model\User
   * @throws \Exception
   */
  protected function generateUser()
  {
    return $this->userAdmin();
  }

  /**
   * Run everything needed to react to input and display data in the way this app is intended
   */
  public function run()
  {
    $this->oUser = $this->generateUser();
  }
}
