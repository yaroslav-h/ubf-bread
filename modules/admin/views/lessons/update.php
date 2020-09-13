<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model admin\models\Lesson */

$this->title = Yii::t('app', 'Update Lesson');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Lessons'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => '#' . ($model->parent_id ?: $model->id), 'url' => ['view', 'id' => $model->parent_id ?: $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="card">

    <div class="card-header"><?= $model->title ?></div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
