<?php
/**
 * Миграция m150716_053551_add_field_user
 *
 * @property string $prefix
 */
 
class m150716_053551_add_field_user extends CDbMigration
{
    // таблицы к удалению, можно использовать '{{table}}'
	public function up(){
        $this->addColumn('users','pass','string');
    }

    public function down(){
        $this->dropColumn('users','pass');
    }
    
}