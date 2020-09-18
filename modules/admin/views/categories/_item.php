<?php

/* @var $this yii\web\View */
/* @var $model admin\models\Category */
/* @var $searchModel admin\search\CategorySearch */
/* @var $key string */
/* @var $index string */

use yii\bootstrap4\Html;
use yii\helpers\Url;

?>

<div class="d-flex justify-content-between align-items-center">
    <div class="text-center">
        <?= \app\components\helpers\StringHelper::lang2locale($model->lang, true) ?>
    </div>
    <div class="w-100 px-3 d-flex">
        <div style="min-width: 25%">
            <?= Html::a($model->name, ['view', 'id' => $model->id, 'return' => Url::current()])?>
        </div>
        <div class="text-muted">
            <?php foreach ($model->getTranslations() as $translation): ?>
                <div class="ml-1"><?= \app\components\helpers\StringHelper::lang2locale($translation->lang, true) ?>: <?= $translation->name?></div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="d-flex align-items-center">
        <?php if(false/*!$model->deleted_at*/): ?>
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
