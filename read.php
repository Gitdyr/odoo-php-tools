<?php

ini_set('display_errors', 1);
error_reporting(E_ALL | E_STRICT);

include('page.php');

$page = new Page();
$page->Display();

?>
