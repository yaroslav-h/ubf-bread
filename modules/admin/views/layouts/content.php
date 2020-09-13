<?php
/* @var $content string */

use yii\bootstrap4\Breadcrumbs;
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header" style="padding: 5px 0.5rem">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">
                        <?= !is_null($this->title) ? \yii\helpers\Html::encode($this->title) : \yii\helpers\Inflector::camelize($this->context->id) ?>
                    </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <?= Breadcrumbs::widget([
                        'homeLink' => ['label' => 'Home', 'url' => ['/admin/default/index']],
                        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                        'options' => [
                            'class' => 'float-sm-right'
                        ]
                    ]) ?>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <?= $content ?><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
