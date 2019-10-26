<?php
namespace Limbonia\Controller;

/**
 * Limbonia Resource Lock Controller class
 *
 * Admin controller for handling site resource locks
 *
 * @author Lonnie Blansett <lonnie@limbonia.tech>
 * @package Limbonia
 */
class ResourceLock extends \Limbonia\Controller
{
  use \Limbonia\Traits\ModelController;

  /**
   * List of column names that should remain static
   *
   * @var array
   */
  protected $aStaticColumn = [];

  /**
   * Generate and return the HTML for the specified form field based on the specified information
   *
   * @param string $sName
   * @param string $sValue
   * @param array $hData
   * @return string
   */
  public function getFormField($sName, $sValue = null, $hData = [])
  {
    if ($sName == 'Resource')
    {
      $oSelect = $this->oApp->widgetFactory('Select', "$this->sType[Resource]");
      $sEmptyModelLabel = $this->isSearch() ? 'None' : 'Select a resource';
      $oSelect->addOption($sEmptyModelLabel, '');

      foreach ($_SESSION['ResourceList'] as $sResource => $hComponent)
      {
        if ($sValue == $sResource)
        {
          $oSelect->setSelected($sResource);
        }

        $oSelect->addOption($sResource);
      }

      return static::widgetField($oSelect, 'Resource');
    }

    if ($sName == 'Component')
    {
      $oSelect = $this->oApp->widgetFactory('Select', "$this->sType[Component]");
      $sEmptyModelLabel = $this->isSearch() ? 'None' : 'Select a component';
      $oSelect->addOption($sEmptyModelLabel, '');

      //since I'm setting the name for the Resource and Component objects above, I can depend on their ids below
      $sScript = "var resource = document.getElementById('{$this->sType}Resource');\n";
      $sScript .= "var component = document.getElementById('{$this->sType}Component');\n";
      $sScript .= "\nfunction updateComponent()\n";
      $sScript .= "{\n";
      $sScript .= "  currentResource = resource.options[resource.selectedIndex].value\n";
      $sScript .= "\n";
      $sScript .= "  with (component)\n";
      $sScript .= "  {\n";
      $sScript .= "    for (i = options.length - 1; i > 0; i--) { options[i] = null; }\n";
      $sScript .= "    switch (currentResource)\n";
      $sScript .= "    {\n";

      foreach ($_SESSION['ResourceList'] as $sResource => $hComponent)
      {
        $sScript .= "      case '".str_replace("'", "\'", $sResource)."':\n";
        $i = 1;

        foreach ($hComponent as $sName => $sDescription)
        {
          $sScript .= "        options[$i] = new Option('" . str_replace("'", "\'", $sName) . ":  " . str_replace("'", "\'", $sDescription) . "', '" . str_replace("'", "\'", $sName) . "');\n";
          $i++;
        }

        $sScript .= "        break;\n";
      }

      $sScript .= "    }\n";
      $sScript .= "  }\n";
      $sScript .= "}\n";
      $sScript .= "\n";
      $sScript .= "resource.onchange = updateComponent\n";
      $sScript .= "updateComponent();\n";
      $sScript .= "\n";
      $sScript .= "for (i = component.options.length - 1; i > 0; i--)\n";
      $sScript .= "{\n";
      $sScript .= "  if (component.options[i].value == '" . str_replace("'", "\'", $sValue) . "')\n";
      $sScript .= "  {\n";
      $sScript .= "    component.selectedIndex = i;\n";
      $sScript .= "  }\n";
      $sScript .= "}\n";

      $oSelect->writeJavascript($sScript);
      return static::widgetField($oSelect, 'Component');
    }

    return parent::getFormField($sName, $sValue, $hData);
  }

  /**
   * Generate the search results table headers in the specified grid object
   *
   * @param \Limbonia\Widget\Table $oSortGrid
   * @param string $sColumn
   */
  public function processSearchGridHeader(\Limbonia\Widget\Table $oSortGrid, $sColumn)
  {
    return parent::processSearchGridHeader($oSortGrid, ($sColumn == 'KeyID' ? 'Name' : $sColumn));
  }

  /**
   * Generate and return the value of the specified column
   *
   * @param \Limbonia\Model $oModel
   * @param string $sColumn
   * @return mixed
   */
  public function getColumnValue(\Limbonia\Model $oModel, $sColumn)
  {
    if ($sColumn == 'KeyID')
    {
      try
      {
        return $this->oApp->modelFromId('ResourceKey', $oModel->keyId)->name;
      }
      catch (Exception $e)
      {
        return '';
      }
    }

    return parent::getColumnValue($oModel, $sColumn);
  }

  /**
   * Return the subject of this controller's current ticket, if there is one
   *
   * @return string
   */
  public function getCurrentModelTitle()
  {
    return $this->oModel->resource . " :: " . $this->oModel->component;
  }
}