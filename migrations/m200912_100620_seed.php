<?php

use yii\db\Migration;

/**
 * Class m200912_100620_seed
 */
class m200912_100620_seed extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->delete('users');
        $this->insert('users', [
            'id' => 1,
            'group' => \app\rbac\RbacEnum::GROUP_ADMIN,
            'name' => 'admin',
            'email' => 'admin',
            'password_hash' => Yii::$app->security->generatePasswordHash('admin'),
            'auth_key' => Yii::$app->security->generateRandomString(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200912_100620_seed cannot be reverted.\n";
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200912_100620_seed cannot be reverted.\n";

        return false;
    }
    */
}
