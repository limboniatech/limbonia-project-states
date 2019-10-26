<?php
namespace Limbonia;

/**
 * Limbonia ModelList class
 *
 * This is an iterable and countable wrapper around the around the result of
 * database search for a set of models
 *
 * @author Lonnie Blansett <lonnie@limbonia.tech>
 * @package Limbonia
 */
class ModelList implements \ArrayAccess, \Countable, \SeekableIterator
{
  use \Limbonia\Traits\HasApp;
  use \Limbonia\Traits\HasDatabase;

  /**
   * Name of the table that the list models come from
   *
	 * @var string
	 */
	protected $sTable = '';

	/**
   * The database result object that contain the models
   *
	 * @var \Limbonia\Interfaces\Result
	 */
	protected $oResult = null;

	/**
	 * Constructor
	 *
	 * @param string $sTable - the name of the table that the list models come from.
	 * @param \Limbonia\Interfaces\Result $oResult - the database result object that contain the models
	 */
	public function __construct($sTable, \Limbonia\Interfaces\Result $oResult)
	{
		$this->sTable = $sTable;
		$this->oResult = $oResult;
	}

  /**
   * Return a hash of all models in this list indexed by their ID
   *
   * @return array
   */
  public function getAll()
  {
    $hList = [];

    foreach ($this as $oModel)
    {
      $hList[$oModel->id] = $oModel->getAll();
    }

    return $hList;
  }

    /**
	 * Attempt to create and return an model based on the data
	 *
	 * @param array $hModel
	 * @return Model
	 */
	protected function getModel(array $hModel = [])
	{
    if (empty($hModel))
    {
      return null;
    }

		$oModel = Model::fromArray($this->sTable, $hModel, $this->getDatabase());

    if ($this->oApp instanceof \Limbonia\App)
    {
      $oModel->setApp($this->oApp);
    }

    return $oModel;
	}

  /**
   * Set the specified array offset with the specified value
   *
   * @note This is an implementation detail of the ArrayAccess Interface
   *
   * @param mixed $xOffset
   * @param mixed $xValue
   */
	public function offsetset($xOffset, $xValue)
	{
		return $this->oResult->offsetset($xOffset, $xValue);
	}

  /**
   * Unset the specified array offset
   *
   * @note This is an implementation detail of the ArrayAccess Interface
   *
   * @param mixed $xOffset
   */
	public function offsetUnset($xOffset)
	{
		return $this->oResult->offsetUnset($xOffset);
	}

  /**
   * Does the specified array offset exist?
   *
   * @note This is an implementation detail of the ArrayAccess Interface
   *
   * @param mixed $xOffset
   * @return boolean
   */
	public function offsetExists($xOffset)
	{
		return $this->oResult->offsetExists($xOffset);
	}

  /**
   * Return the value stored at the specified array offset
   *
   * @note This is an implementation detail of the ArrayAccess Interface
   *
   * @param mixed $xOffset
   * @return mixed
   */
	public function offsetget($xOffset)
	{
		return $this->getModel($this->oResult->offsetget($xOffset));
	}

  /**
   * Return the number of columns represented by this object
   *
   * @note This is an implementation detail of the Countable Interface
   *
   * @return integer
   */
	public function count()
	{
		return $this->oResult->count();
	}

  /**
   * Return the current value of this object's data
   *
   * @note This is an implementation detail of the Iterator Interface
   *
   * @return mixed
   */
	public function current()
	{
		return $this->getModel($this->oResult->current());
	}

  /**
   * Return the key of the current value of this object's data
   *
   * @note This is an implementation detail of the Iterator Interface
   *
   * @return mixed
   */
	public function key()
	{
		return $this->oResult->key();
	}

  /**
   * Move to the next value in this object's data
   *
   * @note This is an implementation detail of the Iterator Interface
   */
	public function next()
	{
		$this->oResult->next();
	}

  /**
   * Rewind to the first model of this object's data
   *
   * @note This is an implementation detail of the Iterator Interface
   */
	public function rewind()
	{
		$this->oResult->rewind();
	}

  /**
   * Is the current value valid?
   *
   * @note This is an implementation detail of the Iterator Interface
   *
   * @return boolean
   */
	public function valid()
	{
		return $this->oResult->valid();
	}

  /**
   * Move the value to the data represented by the specified key
   *
   * @note This is an implementation detail of the SeekableIterator Interface
   *
   * @param mixed $xKey
   * @throws OutOfBoundsException
   */
		public function seek($xKey)
	{
		$this->oResult->seek($xKey);
	}
}