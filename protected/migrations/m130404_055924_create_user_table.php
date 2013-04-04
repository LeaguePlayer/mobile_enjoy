<?php

class m130404_055924_create_user_table extends CDbMigration
{
	public function up()
	{
		$this->createTable('user', array(
            'id' => 'pk',
            'name' => 'string NOT NULL',
            'login' => 'string NOT NULL',
            'pass' => 'string NOT NULL',
            'role' => 'string NOT NULL'
        ));
        $this->insert('user', array(
        	'name' => 'Admin',
        	'login' => 'admin',
        	'pass' => md5('admin'),
        	'role' => 'admin'
        ));
	}

	public function down()
	{
		$this->dropTable('user');
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