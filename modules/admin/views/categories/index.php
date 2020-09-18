<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $searchModel admin\search\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Categories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <?= \admin\widgets\NavPillsIndex::widget(['tabs' => \admin\search\CategorySearch::tabs(), 'active' => $searchModel->tab])?>
            </div>
            <div>
                <?= Html::a(Yii::t('app', 'Create'), ['create', 'return' => \yii\helpers\Url::current()], ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
    </div>

    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'list-group'],
        'itemOptions' => ['class' => 'list-group-item'],
        'itemView' => '_item',
        'layout' => '{items}',
        'emptyTextOptions' => [
            'class' => 'p-3 text-center'
        ]
    ]) ?>


</div>
