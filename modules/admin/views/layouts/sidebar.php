<?php

use app\rbac\RbacEnum;

?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?=\yii\helpers\Url::home()?>" class="brand-link">
        <img src="<?=$assetDir?>/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
             style="opacity: .8">
        <span class="brand-text font-weight-light">UBFBread</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <!--<div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?/*=$assetDir*/?>/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">Alexander Pierce</a>
            </div>
        </div>-->

        <!-- Sidebar Menu -->
        <nav class="mt-2">

            <?= admin\widgets\Menu::widget([
                'items' => [
                    [
                        'label' => Yii::t('app', 'Dashboard'), 'icon' => 'tachometer-alt',
                        'url' => ['/admin/default/index'],
                        'visible' => can(RbacEnum::MODER),
                    ],
                    [
                        'label' => Yii::t('app', 'Lessons'), 'url' => ['/admin/lessons/index'],
                        'active' => Yii::$app->controller->id == 'lessons',
                        'visible' => can(RbacEnum::MODER),
                        'icon' => 'th'/*, 'badge' => '<span class="right badge badge-danger">New</span>'*/
                    ],
                    [
                        'label' => Yii::t('app', 'Users'), 'url' => ['/admin/users/index'],
                        'active' => Yii::$app->controller->id == 'users',
                        'visible' => can(RbacEnum::ADMIN),
                        'icon' => 'user'/*, 'badge' => '<span class="right badge badge-danger">New</span>'*/
                    ],
                    [
                        'label' => Yii::t('app', 'System'), 'header' => true,
                        'visible' => can(RbacEnum::ADMIN),
                    ],
                    [
                        'label' => Yii::t('app', 'Logs'),
                        'icon' => 'list', 'url' => ['/admin/log/index'],
                        'visible' => can(RbacEnum::ADMIN),
                        'badge' => \admin\models\Log::totalCount(\yii\log\Logger::LEVEL_ERROR) ? '<span class="right badge badge-danger">'.\admin\models\Log::totalCount(\yii\log\Logger::LEVEL_ERROR).'</span>' : ''
                    ],
                    /*[
                        'label' => 'Level1',
                        'items' => [
                            ['label' => 'Level2', 'iconStyle' => 'far'],
                            [
                                'label' => 'Level2',
                                'iconStyle' => 'far',
                                'items' => [
                                    ['label' => 'Level3', 'iconStyle' => 'far', 'icon' => 'dot-circle'],
                                    ['label' => 'Level3', 'iconStyle' => 'far', 'icon' => 'dot-circle'],
                                    ['label' => 'Level3', 'iconStyle' => 'far', 'icon' => 'dot-circle']
                                ]
                            ],
                            ['label' => 'Level2', 'iconStyle' => 'far']
                        ]
                    ],*/
                    //['label' => 'Important', 'iconStyle' => 'far', 'iconClassAdded' => 'text-danger'],
                ],
            ]);
            ?>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>