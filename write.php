<?php

include('page.php');

class Search extends Page
{
    public function HandlePost()
    {
        parent::HandlePost();
        if ($_POST) {
            $model = $this->Cookie('model');
            $id = $this->Cookie('id');
            $field_values = $this->Cookie('field_values');
            $fields = array();
            if ($field_values) {
                $values = array();
                $field_values = explode(',', $field_values);
                foreach ($field_values as $field_value) {
                    $f = explode('=', $field_value);
                    if (count($f) == 2) {
                        $field = $f[0];
                        $value = $f[1];
                        $values[$field] = $value;
                        $fields[] = $field;
                    }
                }
                $q = [(int)$id, $values];
            } else {
                $q = [(int)$id, []];
            }
            $res = $this->Connect();
            if ($res) {
                $this->response = $this->ExecKw($model, 'write', $q);
                if ($this->response) {
                    $q = [(int)$id, $fields];
                    $this->response =
                        $this->ExecKw($model, 'read', $q);
                }
            }
        }
    }

    public function Contents($body, $title = '')
    {
        $div = parent::Contents($body, 'Write');
        $this->InputField($div, 'Model');
        $this->InputField($div, 'Id');
        $this->InputField($div, 'Field=Value', 'field_values',
            '(separate with comma to assign to multiple fields)');
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
                $table->Th($key);
                if (is_array($val)) {
                    $val = sprintf('[%s]', implode(', ', $val));
                }
                $val = str_replace("\n", '<br>', $val);
                $table->Td($val)->class('text-break');
            }
        }
    }
}

$page = new Search();
$page->Display();

?>
