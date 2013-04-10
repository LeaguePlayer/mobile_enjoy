<?php

class m130404_053114_create_page_table extends CDbMigration
{
	public function up()
	{
		$this->createTable('page', array(
            'id' => 'pk',
            'title' => 'string NOT NULL',
            'content' => 'text',
        ));
        $this->insert('page', array(
        	'title' => 'Информация',
            'content' => 'Текст',
        ));
	}

	public function down()
	{
		$this->dropTable('page');
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