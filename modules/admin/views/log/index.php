<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel admin\search\LogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Logs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <?= \admin\widgets\NavPillsIndex::widget(['tabs' => \admin\search\LogSearch::tabs(), 'active' => $searchModel->tab]) ?>
            </div>
            <div>
                <?= Html::a(Yii::t('app', 'Clear'), ['clear', 'return' => \yii\helpers\Url::current()], [
                    'class' => 'btn btn-danger',
                    'data-confirm' => Yii::t('app', 'Are you sure you want to clear logs?'),
                    'data-method' => 'post',
                ]) ?>
            </div>
        </div>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{items}\n{pager}",
        'columns' => [
            'id',
//            [
//                'attribute' => 'level',
//                'value' => 'levelName',
//                'filter' => \admin\models\Log::levels()
//            ],
            /*'category',
            'prefix:ntext',*/
            [
                'attribute' => 'category',
                'format' => 'html',
                'value' => function($model) {
                    return Html::tag('div', $model->category) . Html::tag('div', $model->prefix);
                },
                'filter' => $searchModel->getCategories()
            ],
            [
                'attribute' => 'log_time',
                'format' => 'datetime',
                'filter' => Html::activeInput('date', $searchModel, 'date', ['class' => 'form-control'])
            ],
            ['class' => 'admin\widgets\ActionColumn', 'template' => '{view}'],
        ],
    ]); ?>


</div>
