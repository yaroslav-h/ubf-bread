<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model admin\models\User */
/* @var $form yii\bootstrap4\ActiveForm */

$model->password = '';

?>

<div class="card-body">

    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password')
        ->hint($model->isNewRecord ? "" : Yii::t('app', 'Leave empty to leave an old password'))
        ->passwordInput() ?>

    <div class="form-group text-center">
        <?= Html::a(Yii::t('app', 'Cancel'), request()->get('return', ['index']), ['class' => 'btn btn-link']) ?>
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
