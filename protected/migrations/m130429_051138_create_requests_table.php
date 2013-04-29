<?php

class m130429_051138_create_requests_table extends CDbMigration
{
	public function up()
	{
		$this->createTable('requests', array(
            'id' => 'pk',
            'udid' => 'varchar(40) NOT NULL',
            'productid' => 'varchar(200) NOT NULL',
            'email' => 'varchar(100) default NULL',
            'message' => 'TEXT default NULL',
            'status' => 'tinyint(1) NOT NULL default "0"',
            'lastupdated' => 'timestamp NOT NULL default CURRENT_TIMESTAMP',
        ));
	}

	public function down()
	{
		$this->dropTable('requests');
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