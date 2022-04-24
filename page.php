<?php

include('htmlnode.php');

class Page {
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

        $this->Item($ul, 'Search');
        $this->Item($ul, 'Read');
        $this->Item($ul, 'Write');

        $ul = $div->Ul();
        $ul->class('navbar-nav');
        $this->Item($ul, 'Settings');
    }

    public function Contents($body)
    {
    }

    public function Body()
    {
        $body = $this->html->Body();
        $this->Navigation($body);
        $this->Contents($body);
    }

    public function Display()
    {
        $this->html = new HtmlNode();
        $this->Header();
        $this->Body();
        $this->html->Display();
    }
}

?>
