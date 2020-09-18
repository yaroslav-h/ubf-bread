<?php


namespace app\components\traits;


trait MigrationTrait
{
    /**
     * @param bool $autoinc
     * @return string
     */
    public function intPk($autoinc = true)
    {
        return 'BIGINT UNSIGNED PRIMARY KEY' . ($autoinc ? ' AUTO_INCREMENT' : '');
    }

    /**
     * @return \yii\db\ColumnSchemaBuilder
     */
    public function intFk()
    {
        return $this->bigInteger()->unsigned();
    }

    public function createTable($table, $columns, $options = null)
    {
        if($options == null) {
            if ($this->db->driverName === 'mysql') {
                $options = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
            }
        }

        return parent::createTable($table, $columns, $options);
    }

    public function createFk($table, $col, $next_table, $next_col = 'id', $delete = 'CASCADE')
    {
        $this->createIndex('idx-'.$table.'-'.$col, $table, $col);
        $this->addForeignKey('fk-'.$table.'-'.$col, $table, $col, $next_table, $next_col, $delete);
    }
    public function dropFk($table, $col)
    {
        $this->dropForeignKey('fk-'.$table.'-'.$col, $table);
        $this->dropIndex('idx-'.$table.'-'.$col, $table);
    }
}