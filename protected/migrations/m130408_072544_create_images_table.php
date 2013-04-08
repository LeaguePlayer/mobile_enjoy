<?php

class m130408_072544_create_images_table extends CDbMigration
{
	public function up()
	{
		$this->createTable('image', array(
            'id' => 'pk',
            'filename' => 'string NOT NULL',
            'block_id' => 'int NOT NULL',
            'sort' => 'int NOT NULL DEFAULT 0'
        ));
	}

	public function down()
	{
		$this->dropTable('image');
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