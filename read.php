<?php

/**
 * (c) Kjeld Borch Egevang
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

include('page.php');

class Search extends Page
{
    public function HandlePost()
    {
        parent::HandlePost();
        if ($_POST) {
            $model = $this->Cookie('model');
            $id = $this->Cookie('id');
            $fields = $this->Cookie('fields');
            if ($fields) {
                $fields = explode(',', $fields);
                $q = [(int)$id, $fields];
            } else {
                $q = [(int)$id, []];
            }
            $res = $this->Connect();
            if ($res) {
                $this->response =
                    $this->ExecKw($model, 'read', $q);
            }
        }
    }

    public function Contents($body, $title = '')
    {
        $div = parent::Contents($body, 'Read');
        $this->InputField($div, 'Model');
        $this->InputField($div, 'Id');
        $this->InputField($div, 'Fields', 'fields',
            '(separate with comma, empty for all)');
        $this->SubmitButton($div);
        if (!empty($this->response)) {
            $div = $body->Div();
            $div->class('card ms-2 me-2');
            $div = $div->Div();
            $div->class('card-body');
            $div->H5('Results')->class('card-title');
            $table = $div->Table();
            $table->class('table mx-auto w-auto');
            foreach ($this->response[0] as $key => $val) {
                $tr = $table->Tr();
                $tr->Th($key);
                if (is_array($val)) {
                    $val = sprintf('[%s]', implode(', ', $val));
                }
                $val = str_replace("\n", '<br>', $val);
                $tr->Td($val)->class('text-break');
            }
        }
    }
}

$page = new Search();
$page->Display();

?>
