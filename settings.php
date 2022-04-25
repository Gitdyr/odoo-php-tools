<?php

/**
 * (c) Kjeld Borch Egevang
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

ini_set('display_errors', 1);
error_reporting(E_ALL | E_STRICT);

include('page.php');

class Settings extends Page
{
    public function HandlePost()
    {
        if ($_POST) {
            // Clear company ID
            $_POST['company_id'] = 0;
            parent::HandlePost();
            $res = $this->Connect();
            if ($res) {
                $this->info = 'Settings updated';
            }
        }
    }

    public function Contents($body, $title = '')
    {
        $div = parent::Contents($body, 'Settings');
        $this->InputField($div, 'URL');
        $this->InputField($div, 'Database');
        $this->InputField($div, 'User name', 'username');
        $this->InputField($div, 'API key', 'password');
        $this->InputField($div, 'Company');
        $this->SubmitButton($div);
    }
}

$page = new Settings();
$page->Display();

?>
