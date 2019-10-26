<?php
echo "<form name=\"Edit\" action=\"" . $controller->generateUri($currentModel->id, $method) . "\" method=\"post\">\n";

echo $controller->getFormField('RoleID');
echo "<div class=\"field\"><span class=\"blankLabel\"></span><span class=\"data\"><button type=\"submit\" name=\"Update\">Update</button>&nbsp;&nbsp;&nbsp;&nbsp;<a class=\"model\" href=\"" . $controller->generateUri($currentModel->id) . "\"><button name=\"No\">No</button></a></span></div>\n";
echo "</form>";
