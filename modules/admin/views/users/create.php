<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model admin\models\User */

$this->title = Yii::t('app', 'Create User');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card">

    <div class="card-header"><?= Yii::t('app', 'User') ?></div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
