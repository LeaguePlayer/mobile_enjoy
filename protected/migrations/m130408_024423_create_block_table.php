<?php

class m130408_024423_create_block_table extends CDbMigration
{
	public function up()
	{
		$this->createTable('block', array(
            'id' => 'pk',
            'name' => 'string NOT NULL',
            'price' => 'DECIMAL(9, 2) NOT NULL',
            'preview' => 'string',
            'public' => 'BOOLEAN NOT NULL',
            'sort' => 'int NOT NULL DEFAULT 0'
        ));
	}

	public function down()
	{
		$this->dropTable('block');
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