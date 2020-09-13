<?php

/* @var $this yii\web\View */
/* @var $model admin\models\Lesson */
/* @var $searchModel admin\search\LessonSearch */
/* @var $key string */
/* @var $index string */

use yii\bootstrap4\Html;
use yii\helpers\Url;

?>

<div class="d-flex justify-content-between align-items-center">
    <div class="text-center">
        <div class="h5 m-0"><?= Yii::$app->formatter->asDate($model->date, 'php:d')?></div>
        <div class="small"><?= Yii::$app->formatter->asDate($model->date, 'php:m.Y')?></div>
    </div>
    <div class="w-100 px-3">
        <div>
            <?= Html::a($model->title, ['view', 'id' => $model->id, 'return' => Url::current()])?>
        </div>
        <div class="text-muted small"><?= $model->passage ?></div>
    </div>
    <div class="d-flex align-items-center">
        <div>
            <div class="d-flex">
                <div class="badge"><i class="fas fa-fw fa-pen-square"></i> n/a</div>
                <div class="badge"><i class="fas fa-fw fa-eye"></i> n/a</div>
            </div>
            <div class="d-flex justify-content-center">
                <?php foreach ($model->getLangs() as $i => $lang): ?>
                    <div class="badge <?= $i == 0 ? "badge-primary" : "badge-info"?> ml-1"><?= \app\components\helpers\StringHelper::lang2locale($lang, true) ?></div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php if(!$model->deleted_at): ?>
        <div class="ml-2">
            <?= Html::a('<i class="fas fa-fw fa-trash"></i>', ['delete', 'id' => $model->id, 'return' => Url::current()], [
                'class' => 'btn btn-link',
                'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                'data-method' => 'post'
            ])?>
        </div>
        <?php endif; ?>
    </div>
</div>
