<?php
namespace Limbonia\Controller;

/**
 * Limbonia Auth Controller class
 *
 * Admin controller for handling user authentication
 *
 * @author Lonnie Blansett <lonnie@limbonia.tech>
 * @package Limbonia
 */
class Auth extends \Limbonia\Controller
{
  /**
   * The admin group that this controller belongs to
   *
   * @var string
   */
  protected static $sGroup = 'Hidden';

  /**
   * List of valid HTTP methods
   *
   * @var array
   */
  protected static $hHttpMethods =
  [
    'head',
    'get',
    'post',
    'delete',
    'options'
  ];

  /**
   * A list of components the current user is allowed to use
   *
   * @var array
   */
  protected $hAllow =
  [
    'create' => true,
    'delete' => true
  ];

  /**
   * Do whatever setup is needed to make this controller work...
   *
   * @throws Exception on failure
   */
  public function setup()
  {
    $this->oApp->getDB()->createTable('UserAuth', "UserID INTEGER UNSIGNED NOT NULL,
AuthToken VARCHAR(255) NOT NULL,
LastUseTime TIMESTAMP NOT NULL,
INDEX Unique_UserAuth(UserID, AuthToken)");
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
    //check if an other auth controller is active and if so then allow deactivation
    //otherwise throw an exception
    throw new \Limbonia\Exception("The system requires an auth controller so this can *not* be deactivated");
  }

  /**
   * Is the current user valid?
   *
   * @return boolean
   */
  protected function validUser()
  {
    if ($this->oRouter->method == 'post')
    {
      return true;
    }

    return parent::validUser();
  }

  /**
   * Perform the base "GET" code then return null on success
   *
   * @return null
   * @throws \Exception
   */
  protected function processApiHead()
  {
    $this->oApp->getDB();
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
    return $this->oApp->getDB()->query("SELECT * FROM UserAuth");
  }

  /**
   * Run the default "POST" code and return the created data
   *
   * @return array
   * @throws \Exception
   */
  protected function processApiPost()
  {
    $hData = $this->oRouter->data;

    if (empty($hData['email']) || empty($hData['password']))
    {
      throw new \Limbonia\Exception\Web('Authentication failed', null, 401);
    }

    if ($hData['email'] === $this->oApp->master['User'] && $hData['password'] === $this->oApp->master['Password'])
    {
      $oUser = $this->oApp->userByEmail('MasterAdmin');
    }
    else
    {
      $oUser = $this->oApp->userByEmail($hData['email']);
      $oUser->authenticate($hData['password']);
    }

    return
    [
      'auth_token' => $oUser->generateAuthToken(),
      'user' => $oUser->getAll()
    ];
  }

  /**
   * Run the default "DELETE" code and return true
   *
   * @return array
   * @throws \Exception
   */
  protected function processApiDelete()
  {
    $this->oApp->user()->deleteAuthToken($this->oRouter->action);
    return null;
  }
}