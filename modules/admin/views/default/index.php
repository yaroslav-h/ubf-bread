<?php

/* @var $this yii\web\View */

$this->title = Yii::t('app', 'Home');
?>
<div class="default-index">

    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-th"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Lessons</span>
                    <span class="info-box-number"><?= \app\models\Lesson::totalCount() ?></span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Members</span>
                    <span class="info-box-number"><?= \app\models\User::totalCount() ?></span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <!-- fix for small devices only -->
        <div class="clearfix hidden-md-up"></div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-eye"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Reads</span>
                    <span class="info-box-number"><?= \app\models\Lesson::totalCount('reads')?></span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-comment-alt"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Testimonies</span>
                    <span class="info-box-number"><?= \app\models\Lesson::totalCount('testimonies')?></span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->


    </div>

</div>
