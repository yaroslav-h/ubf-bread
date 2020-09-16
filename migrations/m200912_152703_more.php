<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%lesson_read}}`.
 */
class m200912_152703_more extends Migration
{
    use \app\components\traits\MigrationTrait;

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%lesson_read_by_user}}', [
            'lesson_id' => $this->intFk(true),
            'user_id' => $this->intFk(true),
            'read_at' => $this->integer()->notNull(),
            'lang' => $this->integer()->notNull()->defaultValue(0),
            'PRIMARY KEY (lesson_id, user_id)'
        ]);
        $this->createFk('lesson_read_by_user', 'lesson_id', 'lessons');
        $this->createFk('lesson_read_by_user', 'user_id', 'users');

        $this->createTable('{{%testimonies}}', [
            'id' => $this->intPk(),
            'lesson_id' => $this->intFk(true),
            'content_json' => 'mediumtext',
            'is_published' => $this->boolean()->notNull()->defaultValue(0),
            'created_by' => $this->intFk(true),
            'created_at' => $this->integer(),
        ]);
        $this->createFk('testimonies', 'lesson_id', 'lessons');
        $this->createFk('testimonies', 'created_by', 'users');

        $this->addColumn('lessons', 'testimonies_count', $this->integer()->after('is_intro')->notNull()->defaultValue(0));
        $this->addColumn('lessons', 'user_reads_count', $this->integer()->after('testimonies_count')->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('lessons', 'user_reads_count');
        $this->dropColumn('lessons', 'testimonies_count');


        $this->dropFk('testimonies', 'lesson_id');
        $this->dropFk('testimonies', 'created_by');
        $this->dropTable('{{%testimonies}}');


        $this->dropFk('lesson_read_by_user', 'lesson_id');
        $this->dropFk('lesson_read_by_user', 'user_id');
        $this->dropTable('{{%lesson_read_by_user}}');
    }
}
