<?php

/* @var $this yii\web\View */
/* @var $model admin\models\Lesson */
/* @var $searchModel admin\search\LessonSearch */
/* @var $key string */
/* @var $index string */

use yii\bootstrap4\Html;
use yii\helpers\Url;

?>

<div class="d-flex justify-content-between align-items-center p-2">
    <div class="w-100 pl-2">
        <div>
            <?= Html::a($model->name, ['view', 'id' => $model->id, 'return' => Url::current()])?>
        </div>
        <div class="text-muted small"><?= $model->email ?></div>
    </div>
    <div style="white-space: nowrap" class="pr-2">
        <?= $model->group_name ?>
    </div>
    <?php if($model->created_at): ?>
    <div style="white-space: nowrap" class="pr-2">
        <?= Yii::$app->formatter->asDatetime($model->created_at)?>
    </div>
    <?php endif; ?>
    <div class="nowrap d-flex align-items-center">
        <?= \admin\widgets\btn\BtnUpdate::widget(['model' => $model, 'btn' => 'link'])?>
        <?= \admin\widgets\btn\BtnDelete::widget(['model' => $model, 'btn' => 'link'])?>
    </div>
</div>
