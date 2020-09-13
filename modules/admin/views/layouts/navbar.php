
<?php

use yii\helpers\Html;

?>
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <!-- SEARCH FORM -->
    <form class="form-inline ml-3">
        <div class="input-group input-group-sm">
            <?php
            if(request()->get('tab')) {
                echo Html::hiddenInput('tab', request()->get('tab'));
            }
            ?>
            <input class="form-control form-control-navbar" value="<?= request()->get('q') ?>" name="q" type="search" placeholder="Search">
            <div class="input-group-append">
                <?php if(request()->get('q')): ?>
                <a class="btn btn-navbar" href="<?= \yii\helpers\Url::current(['q' => ''])?>">
                    <i class="fas fa-times"></i>
                </a>
                <?php endif; ?>
                <button class="btn btn-navbar" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </form>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Notifications Dropdown Menu -->
        <!--<li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-bell"></i>
                <span class="badge badge-warning navbar-badge">15</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-header">15 Notifications</span>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-envelope mr-2"></i> 4 new messages
                    <span class="float-right text-muted text-sm">3 mins</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-users mr-2"></i> 8 friend requests
                    <span class="float-right text-muted text-sm">12 hours</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-file mr-2"></i> 3 new reports
                    <span class="float-right text-muted text-sm">2 days</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
            </div>
        </li>-->
        <li class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                <span class="d-none d-md-inline"><?= Yii::$app->user->identity->name ?></span>
            </a>
            <ul class="dropdown-menu border-0 shadow dropdown-menu-right">
                <li><?= Html::a('Change password', ['/admin/default/change-password'], ['data-method' => 'post', 'class' => 'dropdown-item']) ?></li>
                <li><?= Html::a('Sign out', ['/admin/default/logout'], ['data-method' => 'post', 'class' => 'dropdown-item']) ?></li>
            </ul>
        </li>
    </ul>
</nav>