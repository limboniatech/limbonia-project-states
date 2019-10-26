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
class ModelList extends ItemList
{
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
	 * Attempt to create and return an item based on the data
	 *
	 * @param array $hItem
	 * @return Item
	 */
	protected function getItem(array $hItem = [])
	{
    return $this->getModel($hItem);
	}
}