<?php

class Node {
    public $nodes = array();
    public $attributes = array();
    public $void = array(
        'area',
        'base',
        'br',
        'col',
        'command',
        'embed',
        'hr',
        'img',
        'input',
        'keygen',
        'link',
        'meta',
        'param',
        'source',
        'track',
        'wbr'
    );
    public $indent = '';

    public function __construct($parent, $tag, $text ='')
    {
        $this->parent = $parent;
        $this->tag = $tag;
        $this->text = $text;
    }

    public function __call($name, $args)
    {
        $val = reset($args);
        if (ctype_upper($name[0])) {
            // New node
            $tag = lcfirst($name);
            $node = new Node($this, $tag, $val);
            $node->indent = $this->indent.'  ';
            $this->nodes[] = $node;
            return $node;
        } else {
            // New attribute
            $this->attributes[$name][] = $val;
            return null;
        }
    }

    public function First($tag)
    {
        $found = null;
        foreach ($this->nodes as $node) {
            if ($node->tag == $tag) {
                $found = $node;
                break;
            }
        }
        return $found;
    }
    
    public function Last($tag)
    {
        $found = null;
        foreach ($this->nodes as $node) {
            if ($node->tag == $tag) {
                $found = $node;
            }
        }
        return $found;
    }
    
    public function Clone($text)
    {
        $node = clone $this;
        $node->text = $text;
        $this->parent->nodes[] = $node;
        return $node;
    }
    
    public function Display()
    {
        if ($this->attributes) {
            printf("%s<%s", $this->indent, $this->tag);
            foreach ($this->attributes as $key => $vals) {
                printf(' %s="%s"', $key, implode(' ', $vals));
            }
            printf(">\n");
        } else {
            printf("%s<%s>\n", $this->indent, $this->tag);
        }
        if ($this->text) {
            printf("%s  %s\n", $this->indent, $this->text);
        }
        foreach ($this->nodes as $node) {
            $node->Display();
        }
        if (!in_array($this->tag, $this->void)) {
            printf("%s</%s>\n", $this->indent, $this->tag);
        }
    }
}


class Html extends Node {
    public function __construct()
    {
        parent::__construct(null, 'html');
    }

    public function Display()
    {
        header('Content-Type: text/html;charset=UTF-8');
        print "<!DOCTYPE html>\n";
        parent::Display();
    }
}


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
        /*
        $link = $head->Link();
        $link->rel('stylesheet');
        $link->href('css/style.css');
        */
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
        $this->html = new Html();
        $this->Header();
        $this->Body();
        $this->html->Display();
    }
}

?>
