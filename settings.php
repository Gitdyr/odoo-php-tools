<?php

ini_set('display_errors', 1);
error_reporting(E_ALL | E_STRICT);

include('page.php');

class Settings extends Page
{
    public function InputField($div, $title, $name = null)
    {
        if ($name == null) {
            $name = strtolower($title);
        }
        $div = $div->Div();
        $div->class('row mb-3');
        $label = $div->Label($title);
        $label->class('col-sm-4 col-form-label');
        $label->class('text-right');
        $idiv = $div->Div();
        $idiv->class('col-sm-8');
        $input = $idiv->Input();
        $input->class('form-control');
        $input->name($name);
        $input->type('text');
        $input->value($this->Cookie($name));
    }


    public function HandlePost()
    {
        if (!$_POST) {
            return;
        }
        foreach ($_POST as $key => $val) {
            setcookie($key, $val, time() + 0x2000000); // More than one year
            $_COOKIE[$key] = $val;
        }
        $key = 'company_id';
        $val = 0;
        setcookie($key, $val, time() + 0x2000000); // More than one year
        $_COOKIE[$key] = $val;
        // $this->debug = true;
        $this->Connect();
    }

    public function Contents($body)
    {
        $form = $body->Form();
        $form->method('post');
        $div = $form->Div();
        $div->class('card mx-auto');
        $div->style('width: 40rem;');
        $div = $div->Div();
        $div->class('card-body');
        $div->H5('Settings')->class('card-title');

        $this->InputField($div, 'URL');
        $this->InputField($div, 'Database');
        $this->InputField($div, 'User name', 'username');
        $this->InputField($div, 'API key', 'password');
        $this->InputField($div, 'Company');
        $button = $div->Button('Submit');
        $button->type('submit');
        $button->class('btn btn-primary float-end');
    }
}

$page = new Settings();
$page->Display();

?>
