<?php

/**
 * (c) Kjeld Borch Egevang
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

include("page.php");

class Index extends Page
{
    public function Contents($body, $title = '')
    {
        $div = parent::Contents($body, 'Tools');
        $div->Div('This is a simple set of tools to search,
            read and write entries in the Odoo database.<br><br>
            No data are saved on the server. All entered fields are
            saved in cookies locally in your browser.<br><br>
            You can download and install the code from GitHub:');
        $url = 'https://github.com/Gitdyr/odoo-php-tools';
        $div->A($url)->href($url);
    }
}

$page = new Index();
$page->Display();

?>
