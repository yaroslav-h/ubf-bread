<?php

use yii\db\Migration;

/**
 * Class m200910_191820_init
 */
class m200910_191820_init extends Migration
{

    use \app\components\traits\MigrationTrait;

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('users', [
            'id' => $this->intPk(),
            'name' => $this->string()->notNull(),
            'email' => $this->string()->notNull()->unique(),
            'password_hash' => $this->string(64)->notNull()->defaultValue(''),
            'password_reset_token' => $this->string(64)->notNull()->defaultValue(''),
            'auth_key' => $this->string(32)->defaultValue(''),
            'created_at' => $this->integer(),
            'deleted_at' => $this->integer(),
        ]);

        $this->createTable('lessons', [
            'id' => $this->intPk(),
            'parent_id' => $this->intFk(),
            'lang' => $this->tinyInteger()->notNull(),
            'date' => $this->date()->notNull(),
            'title' => $this->string(1024)->notNull(),
            'passage_json' => $this->string(128),
            'content_json' => 'MEDIUMTEXT',
            'is_intro' => $this->boolean()->notNull()->defaultValue(0),
            'created_by' => $this->intFk(),
            'created_at' => $this->integer(),
            'deleted_at' => $this->integer(),
        ]);

        $this->createFk('lessons', 'parent_id', 'lessons');
        $this->createFk('lessons', 'created_by', 'users');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        echo "m200910_191820_init cannot be reverted.\n";

        return false;
    }
}
