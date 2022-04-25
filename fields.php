<?php

/**
 * (c) Kjeld Borch Egevang
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

include('page.php');

class Fields extends Page
{
    public function HandlePost()
    {
        parent::HandlePost();
        if ($_POST) {
            $this->fields = ['string', 'type', 'help'];
            $model = $this->Cookie('model');
            $q = [];
            $f = ['attributes' => $this->fields];
            $res = $this->Connect();
            if ($res) {
                $this->response =
                    $this->ExecKw($model, 'fields_get', $q, $f);
            }
        }
    }

    public function Contents($body, $title = '')
    {
        $div = parent::Contents($body, 'Fields');
        $this->InputField($div, 'Model');
        $this->SubmitButton($div);
        $this->Result($body);
    }
}

$page = new Fields();
$page->Display();

?>
