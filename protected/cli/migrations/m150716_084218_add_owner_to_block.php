<?php
/**
 * Миграция m150716_084218_add_owner_to_block
 *
 * @property string $prefix
 */
 
class m150716_084218_add_owner_to_block extends CDbMigration
{

	public function up(){
        $this->addColumn('block','owner','int');
    }

    public function down(){
        $this->dropColumn('block','owner');
    }
}