<?php
echo "<form name=\"Edit\" action=\"" . $controller->generateUri($currentModel->id, $method) . "\" method=\"post\">\n";

$hResourceKeys = $currentModel->getResourceKeys();

foreach ($currentModel->getResourceList() as $key)
{
  $sValue = isset($hResourceKeys[$key->id]) ? $hResourceKeys[$key->id] : '';
  echo "<div class=\"field\"><span class=\"label\">$key->name</span><span class=\"data\"><input name=\"" . $controller->getType() . "[ResourceKey][$key->id]\" value=\"$sValue\"></span></div>\n";
}

echo "<div class=\"field\"><span class=\"blankLabel\"></span><span class=\"data\"><button type=\"submit\" name=\"Update\">Update</button>&nbsp;&nbsp;&nbsp;&nbsp;<a class=\"model\" href=\"" . $controller->generateUri($currentModel->id) . "\"><button name=\"No\">No</button></a></span></div>\n";
echo "</form>";
