<?php


namespace admin\widgets\btn;


use Yii;
use yii\bootstrap4\Html;
use yii\bootstrap4\Widget;
use yii\db\ActiveRecord;
use yii\helpers\Url;

class BtnUpdate extends Widget
{

    /**
     * @var ActiveRecord
     */
    public $model;

    public $ml;

    public $btn = 'primary';

    public $sm = false;

    public function run()
    {
        if($this->model->hasAttribute('deleted_at') && $this->model->getAttribute('deleted_at') > 0) {
            return null;
        }

        if($this->ml) {
            Html::addCssClass($this->options, "ml-{$this->ml}");
        }

        return Html::tag('div', Html::a('<i class="fas fa-fw fa-edit"></i>', ['update', 'id' => $this->model->id, 'return' => Url::current()], [
            'class' => 'btn btn-' . $this->btn . ($this->sm ? " btn-sm" : ""),
        ]), $this->options);
    }

}