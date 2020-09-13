<?php


namespace admin\widgets;


use yii\bootstrap4\Nav;

class NavPillsIndex extends Nav
{
    public $options = ['class' =>'nav-pills'];

    public $tabs;

    public $active;

    public function init()
    {
        if($this->tabs) {
            $this->items = array_map(function($tab) {
                return [
                    'label' => $tab['label'],
                    'url' => ['index', 'tab' => $tab['key'], 'q' => request()->get('q')],
                    'active' => $this->active == $tab['key']
                ];
            }, $this->tabs);
        }

        parent::init();
    }
}