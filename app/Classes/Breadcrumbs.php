<?php
/**
 * Created by PhpStorm.
 * User: zerotul
 * Date: 09.08.18
 * Time: 23:43
 */

namespace App\Classes;


class Breadcrumbs implements \Iterator
{
    private array $breadcrumbs = [];
    private string $divider = '-';

    public function __construct($text = '', $url = '')
    {
        if ($text) {
            $this->breadcrumbs[] = [$text => $url];
        }
    }

    public function add($text, $url)
    {
        $this->breadcrumbs[] = [$text => $url];
        return $this;
    }

    public function setDivider(string $divider)
    {
        $this->divider = $divider;
    }

    public function render()
    {
        $htmlDivider = '<span class="breadcrumb-divider">' . $this->divider . '</span>';
        $htmlOpenOL = '<ol class="breadcrumbs" itemscope itemtype=http://schema.org/BreadcrumbList>';
        $htmlCloseOL = '</ol>';
        $htmlOpenLI = '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
        $htmlCloseLI = '</li>';
        $html = '';


        $allKey = array_keys($this->breadcrumbs);
        $lastKey = end($allKey);
        foreach ($this->breadcrumbs as $key => $breadcrumb) {
            $crumbs = '<a class="breadcrumb-item" itemscope itemtype="http://schema.org/Thing" itemprop="item" itemid="'. current($breadcrumb) .'" href="' .
                current($breadcrumb) .
                '"><span itemprop="name">' .
                key($breadcrumb) .
                '</span></a><meta itemprop="position" content="' . ($key + 1) . '" />';

            if ($key == $lastKey) {
                $crumbs = '<div class="breadcrumb-item" itemscope itemtype="http://schema.org/Thing" itemprop="item" itemid="'. current($breadcrumb) .
                    '"><span itemprop="name">' .
                    key($breadcrumb) .
                    '</span></div><meta itemprop="position" content="' . ($key + 1) . '" />';
                $html = $html . $htmlOpenLI . $crumbs . $htmlCloseLI;
            } else {
                $crumbs = '<a class="breadcrumb-item" itemscope itemtype="http://schema.org/Thing" itemprop="item" itemid="'. current($breadcrumb) .'" href="' .
                    current($breadcrumb) .
                    '"><span itemprop="name">' .
                    key($breadcrumb) .
                    '</span></a><meta itemprop="position" content="' . ($key + 1) . '" />';
                $html = $html . $htmlOpenLI . $crumbs . $htmlCloseLI . $htmlDivider;
            }
        }

        $html = $htmlOpenOL . $html . $htmlCloseOL;
        return $html;
    }

    public function rewind()
    {
        reset($this->breadcrumbs);
    }

    public function current()
    {
        $var = current($this->breadcrumbs);
        $newVar['name'] = $var[0];
        $newVar['url'] = $var[1];
        return $newVar;
    }

    public function key()
    {
        $var = key($this->breadcrumbs);
        return $var['key'];
    }

    public function next()
    {
        $var = next($this->breadcrumbs);
        return $var;
    }

    public function valid()
    {
        $key = key($this->breadcrumbs);
        $var = ($key !== NULL && $key !== FALSE);
        return $var;
    }

}