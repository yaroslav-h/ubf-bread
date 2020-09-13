<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model admin\models\Lesson */

$this->title = Yii::t('app', 'Create Lesson');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Lessons'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card">

    <div class="card-header"><?= Yii::t('app', 'Lesson') ?></div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
