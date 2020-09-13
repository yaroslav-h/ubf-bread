<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */
/* @var $model app\models\forms\ChangePasswordForm */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

$this->title = Yii::t('app', 'Change password');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card">
    <div class="card-header"><?= Html::encode($this->title) ?></div>

    <div class="card-body">
        <?php $form = ActiveForm::begin([
            // 'layout' => 'horizontal',
        ]); ?>

        <?= $form->field($model, 'current_password')->passwordInput() ?>
        <?= $form->field($model, 'new_password')->passwordInput() ?>
        <?= $form->field($model, 'repeat_new_password')->passwordInput() ?>

        <div class="form-group text-center">
            <?= Html::a(Yii::t('app', 'Cancel'), request()->get('return', Yii::$app->homeUrl), ['class' => 'btn btn-link']) ?>
            <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>
