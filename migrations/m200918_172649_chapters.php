<?php

use yii\db\Migration;

/**
 * Class m200918_172649_chapters
 */
class m200918_172649_chapters extends Migration
{
    use \app\components\traits\MigrationTrait;

    public function up()
    {
        $this->addColumn('lessons', 'chapter_id', $this->intFk()->after('parent_id'));

        $this->createTable('categories', [
            'id' => $this->intPk(),
            'parent_id' => $this->intFk(),
            'type' => $this->smallInteger()->unsigned()->notNull()->defaultValue(0),
            'lang' => $this->tinyInteger()->notNull(),
            'name' => $this->string(128)->notNull(),
            'alt'  => $this->smallInteger()->unsigned()->notNull()->defaultValue(0),
            'order'=> $this->smallInteger()->unsigned()->notNull()->defaultValue(0),
        ]);

        $this->createFk('categories', 'parent_id', 'categories');
        $this->createFk('lessons', 'chapter_id', 'categories');
    }

    public function down()
    {
        $this->dropFk('lessons', 'chapter_id');
        $this->dropFk('categories', 'parent_id');

        $this->dropTable('categories');

        $this->dropColumn('lessons', 'chapter_id');
    }
}
