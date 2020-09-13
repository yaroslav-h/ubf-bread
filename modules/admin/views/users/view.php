<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model admin\models\User */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="card">

    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div><?= Html::encode($this->title) ?></div>
            <div class="d-flex">
                <?= \admin\widgets\btn\BtnUpdate::widget(['model' => $model])?>
                <?= \admin\widgets\btn\BtnDelete::widget(['model' => $model, 'ml' => 2])?>
            </div>
        </div>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'email:email',
            'created_at:datetime',
            'deleted_at:datetime',
        ],
    ]) ?>

</div>
