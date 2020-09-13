<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model admin\models\Lesson */
/* @var $form yii\bootstrap4\ActiveForm */

$only = request()->get('only');

?>

<div class="card-body">

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal'
    ]); ?>

    <?php if($only === 'settings' && !$model->isNewRecord): ?>
        <?= $form->field($model, 'date')->textInput([
            'type' => 'date',
            'disabled' => !!$model->parent
        ]) ?>

        <?= $form->field($model, 'is_intro')->checkbox(['disabled' => !!$model->parent]) ?>
    <?php else: ?>

        <?php if(!($only === 'text' && !$model->isNewRecord)): ?>
            <?= $form->field($model, 'lang')->dropDownList($model->getAvailableLocales(), [
                'disabled' => !!$model->parent
            ]) ?>

            <?php if(!$model->parent): ?>
                <?= $form->field($model, 'date')->textInput([
                    'type' => 'date',
                ]) ?>
            <?php endif; ?>
        <?php endif; ?>

        <?= $form->field($model, 'title')->hint(ArrayHelper::getValue($model, 'parent.title'))->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'passage')->hint(ArrayHelper::getValue($model, 'parent.passage'))->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'content_key_verse')->textarea(['rows' => 3]) ?>
        <?= $form->field($model, 'content_body')->widget(vova07\imperavi\Widget::class, [
            'settings' => [
                'lang' => explode('-', Yii::$app->language)[0],
                'minHeight' => 400,
                'plugins' => [
                    'fullscreen',
                ],
            ]
        ]) ?>
        <?= $form->field($model, 'content_prayer')->hint(ArrayHelper::getValue($model, 'parent.content_prayer'))->textarea(['rows' => 2]) ?>
        <?= $form->field($model, 'content_one_word')->hint(ArrayHelper::getValue($model, 'parent.content_one_word'))->textInput() ?>

        <?php if(!($only === 'text' && !$model->isNewRecord)): ?>
            <?= !$model->parent ? $form->field($model, 'is_intro')->checkbox() : '' ?>
        <?php endif; ?>

    <?php endif; ?>

    <div class="form-group text-center">
        <?= Html::a(Yii::t('app', 'Cancel'), request()->get('return', ['index']), ['class' => 'btn btn-link']) ?>
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
