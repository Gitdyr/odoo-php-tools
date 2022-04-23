<?php

include("page.php");


ini_set('display_errors', 1);
error_reporting(E_ALL | E_STRICT);

$page = new Page();
$page->Display();

?>
