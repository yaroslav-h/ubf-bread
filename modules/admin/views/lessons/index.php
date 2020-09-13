<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $searchModel admin\search\LessonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Lessons');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <?= \admin\widgets\NavPillsIndex::widget(['tabs' => \admin\search\LessonSearch::tabs(), 'active' => $searchModel->tab])?>
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
        'layout' => '{items}',
        'itemView' => '_item',
        'emptyTextOptions' => [
            'class' => 'p-3 text-center'
        ]
    ]) ?>
    <?= \yii\bootstrap4\LinkPager::widget([
        'pagination' => $dataProvider->pagination,
        'listOptions' => [
            'class' => 'pagination p-2 m-0'
        ]
    ])?>
</div>
