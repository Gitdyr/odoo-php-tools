<?php

include('page.php');

class Search extends Page
{
    public function HandlePost()
    {
        parent::HandlePost();
        if ($_POST) {
            $model = $this->Cookie('model');
            $field = $this->Cookie('field');
            $operand = $this->Cookie('operand');
            $value = $this->Cookie('value');
            $fields = $this->Cookie('fields');
            if ($field && $operand && $value) {
                $q = [[[$field, $operand, $value]]];
            } else {
                $q = [];
            }
            if ($fields) {
                $this->fields = explode(',', $fields);
                $f = ['fields' => $this->fields];
            } else {
                $this->fields = [];
                $f = [];
            }
            $res = $this->Connect();
            if ($res) {
                $this->response =
                    $this->ExecKw($model, 'search_read', $q, $f);
            }
        }
    }

    public function Contents($body, $title = '')
    {
        $div = parent::Contents($body, 'Search');
        $this->InputField($div, 'Model');
        $this->InputField($div, 'Field');
        $this->InputField($div, 'Operand');
        $this->InputField($div, 'Value');
        $this->InputField($div, 'Fields to display', 'fields',
            '(separate with comma)');
        $this->SubmitButton($div);
        $this->Result($body);
    }
}

$page = new Search();
$page->Display();

?>
