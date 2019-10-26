<?php
if (isset($failure))
{
  echo "<div class=\"methodFailure\">$failure</div>\n";
}

echo "Please enter the new password below:
<form name=\"Edit\" action=\"" . $controller->generateUri($method) . "\" method=\"post\">
" . $controller->getFormField('Password', '', ['Type' => 'password'], true) . "
" . \Limbonia\Controller::field('<button type="submit" name="Update">Update</button>&nbsp;&nbsp;&nbsp;&nbsp;<a class="model" href="' . $controller->generateUri() . '"><button name="No">No</button></a>') . "
</form>\n";