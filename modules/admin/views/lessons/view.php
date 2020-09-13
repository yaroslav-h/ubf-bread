<?php

use app\components\helpers\StringHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Lesson */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Lessons'), 'url' => ['index']];
$this->params['breadcrumbs'][] = '#' . ($model->parent_id ?: $model->id);

?>
<div class="card">

    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <ul class="nav nav-pills" role="tablist">
                    <li class="nav-item">
                        <?= Html::a(StringHelper::lang2locale($model->lang, true), "#lang{$model->lang}", ['class' => 'nav-link active', 'data-toggle' => 'tab', 'role' => 'tab'])?>
                    </li>
                    <?php foreach ($model->children as $child): ?>
                        <li class="nav-item">
                            <?= Html::a(StringHelper::lang2locale($child->lang, true), "#lang{$child->lang}", ['class' => 'nav-link', 'data-toggle' => 'tab', 'role' => 'tab'])?>
                        </li>
                    <?php endforeach; ?>
                    <?php if(count($model->children) + 1 != count(Yii::$app->params['locales'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                            Add
                        </a>
                        <div class="dropdown-menu">
                            <?php foreach (Yii::$app->params['locales'] as $lang => $name): if(in_array($lang, $model->getLangs())) continue; ?>
                            <?= Html::a($name, ['create', 'parent_id' => $model->id, 'lang' => $lang, 'return' => \yii\helpers\Url::current()], ['class' => 'dropdown-item'])?>
                            <?php endforeach; ?>
                        </div>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="d-flex align-items-center">
                <div>
                    <i class="fas fa-fw fa-calendar"></i> <?= Yii::$app->formatter->asDate($model->date)?>
                </div>
                <div class="d-flex ml-3">
                    <?= Html::a('<i class="fas fa-fw fa-cog"></i>', ['update', 'id' => $model->id, 'only' => 'settings', 'return' => \yii\helpers\Url::current()], ['class' => 'btn btn-default'])?>
                    <div class="dropdown ml-2">
                        <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-fw fa-edit"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <?php foreach (array_merge([$model], $model->children) as $child): ?>
                                <?= Html::a(StringHelper::lang2locale($child->lang), ['update', 'id' => $child->id, 'only' => 'text', 'return' => \yii\helpers\Url::current()], ['class' => 'dropdown-item'])?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="dropdown ml-2">
                        <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-fw fa-trash"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <?= Html::a(Yii::t('app', 'This lesson'), ['delete', 'id' => $model->id, 'return' => \yii\helpers\Url::current()], [
                                'class' => 'dropdown-item',
                                'data' => [
                                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                                    'method' => 'post',
                                ],
                            ])?>
                            <?php foreach ($model->children as $child): ?>
                                <?= Html::a(Yii::t('app', 'Only') . ": " . StringHelper::lang2locale($child->lang), ['delete', 'id' => $child->id, 'return' => \yii\helpers\Url::current()], [
                                    'class' => 'dropdown-item',
                                    'data' => [
                                        'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                                        'method' => 'post',
                                    ],
                                ])?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="tab-content">
        <?php /** @var \app\models\Lesson $item */
        foreach (array_merge([$model], $model->children) as $i => $item): ?>
        <div class="tab-pane <?= $i === 0 ? "active" : ""?>" id="lang<?= $item->lang ?>" role="tabpanel">
            <div class="p-2">
                <div class="title">
                    <blockquote class="blockquote m-0 border-0 p-0">
                        <p class="mb-0"><?= $item->title ?></p>
                        <small>
                            <?= Html::a('<i class="fas fa-fw fa-book-open"></i>', StringHelper::getLink2Passage($item->passage), ['target' => '_blank'])?>
                            <?= $item->passage ?>
                            <?= $model->is_intro ? Html::tag('span', Yii::t('app', 'Intro'), ['class' => 'ml-1 badge badge-success']) : "" ?>
                        </small>
                    </blockquote>
                </div>
                <div class="lead mt-2 mb-2"><?= $item->content_key_verse ?></div>
                <div><?= $item->content_body ?></div>
                <div><strong><?= Yii::t('app', 'Prayer')?>:</strong> <?= $item->content_prayer ?></div>
                <div><strong><?= Yii::t('app', 'One word')?>:</strong> <?= $item->content_one_word ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

</div>
