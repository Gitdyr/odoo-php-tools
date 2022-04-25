<?php

/**
 * (c) Kjeld Borch Egevang
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

ini_set('display_errors', 1);
error_reporting(E_ALL | E_STRICT);

include('htmlnode.php');
include('xmlrpc.php');

class Page {
    public $debug = false;
    public $error = false;
    public $info = false;

    public function Header()
    {
        $bpath = 'bootstrap';
        $head = $this->html->Head();
        $meta = $head->Meta();
        $meta->name("viewport");
        $meta->content("width=device-width, initial-scale=1");
        $head->Title('Odoo Tools');
        $link = $head->Link();
        $link->rel('stylesheet');
        $link->href($bpath.'/css/bootstrap.min.css');
        $link = $head->Link();
        $link->rel('stylesheet');
        $link->href('style.css');
    }

    public function Item($ul, $name, $href = null)
    {
        if ($href == null) {
            $href = strtolower($name).'.php';
        }
        $li = $ul->Li();
        $li->class('nav-item');
        $a = $li->A($name);
        $a->class('nav-link');
        $a->href($href);
        if ($href == basename($_SERVER['SCRIPT_FILENAME'])) {
            $a->class('active');
        }
    }

    public function HandlePost()
    {
        if ($_POST) {
            if (!empty($_GET['debug'])) {
                $this->debug = true;
            }
            foreach ($_POST as $key => $val) {
                // Save cookie for more than one year
                setcookie($key, $val, time() + 0x2000000);
                $_COOKIE[$key] = $val;
            }
        }
    }

    public function InputField($div, $title, $name = null, $text = null)
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
        if ($text) {
            $idiv->Div($text)->class('form-text');
        }
    }

    public function SubmitButton($div)
    {
        $button = $div->Button('Submit');
        $button->type('submit');
        $button->class('btn btn-primary float-end');
    }

    public function Navigation($body)
    {
        $header = $body->Header();
        $nav = $header->Nav();
        $nav->class('navbar navbar-expand-sm navbar-dark bg-dark');
        $div = $nav->Div();
        $div->class('container-fluid');
        $a = $div->A('Odoo tools');
        $a->class('navbar-brand');
        $a->href('index.php');
        $ul = $div->Ul();
        $ul->class('navbar-nav me-auto');

        $this->Item($ul, 'Fields');
        $this->Item($ul, 'Search');
        $this->Item($ul, 'Read');
        $this->Item($ul, 'Write');

        $ul = $div->Ul();
        $ul->class('navbar-nav');
        $this->Item($ul, 'Settings');
    }

    public function Alert($body)
    {
        if ($this->error) {
            $div = $body->Div();
            $div->class('card mx-auto alert alert-danger');
            $div->style('width: 40rem;');
            $div = $div->Div();
            $div->class('card-body');
            $div->Div($this->error);
        } elseif ($this->info) {
            $div = $body->Div();
            $div->class('card mx-auto alert alert-success');
            $div->style('width: 40rem;');
            $div = $div->Div();
            $div->class('card-body');
            $div->Div($this->info);
        }
    }

    public function Result($body)
    {
        if (!empty($this->response)) {
            $div = $body->Div();
            $div->class('card ms-2 me-2');
            $div = $div->Div();
            $div->class('card-body');
            $div->H5('Results')->class('card-title');
            $table = $div->Table();
            $table->class('table');
            $tr = $table->Tr();
            $tr->Th('index');
            foreach ($this->fields as $field) {
                $tr->Th($field);
            }
            foreach ($this->response as $key => $row) {
                $tr = $table->Tr();
                $tr->Td($key);
                foreach ($this->fields as $field) {
                    $val = @$row[$field];
                    if (is_array($val)) {
                        $val = sprintf('[%s]', implode(', ', $val));
                    }
                    $val = str_replace("\n", '<br>', $val);
                    $tr->Td($val);
                }
            }
        }
    }

    public function Contents($body, $title = '')
    {
        $form = $body->Form();
        $form->method('post');
        $div = $form->Div();
        $div->class('card mx-auto');
        $div->style('width: 40rem;');
        $div = $div->Div();
        $div->class('card-body');
        $div->H5($title)->class('card-title');
        return $div;
    }

    public function Body()
    {
        $body = $this->html->Body();
        $this->Navigation($body);
        $this->Alert($body);
        $this->Contents($body);
    }

    public function Display()
    {
        $this->HandlePost();
        $this->html = new HtmlNode();
        $this->Header();
        $this->Body();
        $this->html->Display();
    }

    public function Dump($data)
    {
        printf("<pre>%s</pre>\n", print_r($data, true));
    }

    public function Cookie($name)
    {
        if (isset($_COOKIE[$name])) {
            $val = $_COOKIE[$name];
        } else {
            $val = '';
        }
        return $val;
    }

    public function Connect()
    {
        $this->xml_rpc = new XmlRpc(
            $this->Cookie('url'),
            $this->Cookie('database'),
            $this->Cookie('username'),
            $this->Cookie('password'),
            $this->Cookie('company_id'),
            $this->debug
        );
        if (empty($this->xml_rpc->error) &&
            empty($this->Cookie('company_id')))
        {
            $company = $this->Cookie('company');
            $response = $this->xml_rpc->ExecKw(
                'res.company',
                'search',
                [[['name', '=', $company]]]
            );
            if ($response) {
                $key = 'company_id';
                $val = reset($response);
                setcookie($key, $val, time() + 0x2000000); // More than one year
                $_COOKIE[$key] = $val;
                $this->xml_rpc->company_id = $val;
            } else {
                $this->error = 'Company not found: '.$company;
                return false;
            }
        }
        $this->error = $this->xml_rpc->error;
        return $this->xml_rpc->uid;
    }

    public function ExecKw($model, $method, $parm_list, $parm_dict = [])
    {
        $response =
            $this->xml_rpc->ExecKw( $model, $method, $parm_list, $parm_dict);
        $this->error = $this->xml_rpc->error;
        return $response;
    }
}

?>
