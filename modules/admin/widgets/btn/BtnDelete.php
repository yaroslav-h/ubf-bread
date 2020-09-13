<?php


namespace admin\widgets\btn;


use Yii;
use yii\bootstrap4\Html;
use yii\bootstrap4\Widget;
use yii\db\ActiveRecord;
use yii\helpers\Url;

class BtnDelete extends Widget
{

    /**
     * @var ActiveRecord
     */
    public $model;

    public $ml;

    public $btn = 'danger';

    public $sm = false;

    public function run()
    {
        if($this->model->hasAttribute('deleted_at') && $this->model->getAttribute('deleted_at') > 0) {
            return null;
        }

        if($this->ml) {
            Html::addCssClass($this->options, "ml-{$this->ml}");
        }

        return Html::tag('div', Html::a('<i class="fas fa-fw fa-trash"></i>', ['delete', 'id' => $this->model->id, 'return' => Url::current()], [
            'class' => 'btn btn-' . $this->btn . ($this->sm ? " btn-sm" : ""),
            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
            'data-method' => 'post'
        ]), $this->options);
    }

}