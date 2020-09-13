<?php


namespace app\commands;


use app\rbac\RbacEnum;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class RbacController extends Controller
{

    public function actionIndex()
    {
        $auth = Yii::$app->authManager;

        $auth->removeAll();
        $this->stdout('Removed all auth data.' . PHP_EOL, Console::FG_YELLOW);

        $rule = new \app\rbac\UserGroupRule;
        $auth->add($rule);
        $this->stdout(' - add rule: ' . $rule->name . PHP_EOL, Console::FG_GREEN);

        $user = $auth->createRole(RbacEnum::USER);
        $user->ruleName = $rule->name;
        $auth->add($user);
        $this->stdout(' - add role: ' . $user->name . PHP_EOL, Console::FG_GREEN);

        $moderator = $auth->createRole(RbacEnum::MODER);
        $moderator->ruleName = $rule->name;
        $auth->add($moderator);
        $this->stdout(' - add role: ' . $moderator->name . PHP_EOL, Console::FG_GREEN);
        $auth->addChild($moderator, $user);

        $admin = $auth->createRole(RbacEnum::ADMIN);
        $admin->ruleName = $rule->name;
        $auth->add($admin);
        $this->stdout(' - add role: ' . $admin->name . PHP_EOL, Console::FG_GREEN);
        $auth->addChild($admin, $moderator);

        $this->stdout('Done.' . PHP_EOL, Console::FG_YELLOW);
    }

}