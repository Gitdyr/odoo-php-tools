<?php

ini_set('display_errors', 1);
error_reporting(E_ALL | E_STRICT);

include('page.php');

class Settings extends Page
{
    public function Contents($body)
    {
        $form = $body->Form();

        $div = $form->Div();
        $div->class('card mx-auto');
        $div->style('width: 40rem;');
        $bdiv = $div->Div();
        $bdiv->class('card-body');

        $div = $bdiv->Div();
        $div->class('row mb-3');
        $label = $div->Label('User');
        $label->class('col-sm-4 col-form-label');
        $label->class('text-right');
        $idiv = $div->Div();
        $idiv->class('col-sm-8');
        $input = $idiv->Input();
        $input->class('form-control');

        $div = $bdiv->Div();
        $div->class('row mb-3');
        $label = $div->Label('Password');
        $label->class('col-sm-4 col-form-label');
        $label->class('text-right');
        $idiv = $div->Div();
        $idiv->class('col-sm-8');
        $input = $idiv->Input();
        $input->class('form-control');

    }
}

$page = new Settings();
$page->Display();

?>
