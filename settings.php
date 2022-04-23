<?php

ini_set('display_errors', 1);
error_reporting(E_ALL | E_STRICT);

include('page.php');

class Settings extends Page
{
    public function Contents($body)
    {
    }
}

$page = new Settings();
$page->Display();

?>
