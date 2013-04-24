<?php

class m130423_114941_create_template_table extends CDbMigration
{
	public function up()
	{
		$this->createTable('template', array(
            'id' => 'pk',
            'name' => 'string NOT NULL',
            'json' => 'TEXT NOT NULL'
        ));
	}

	public function down()
	{
		$this->dropTable('template');
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}