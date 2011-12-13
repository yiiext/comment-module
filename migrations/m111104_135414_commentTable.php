<?php

/**
 * This migration creates the comment table
 *
 * @author Carsten Brandt <mail@cebe.cc>
 * @package yiiext.modules.comment
 */
class m111104_135414_commentTable extends CDbMigration
{
	public $userColumType = 'bigint unsigned DEFAULT NULL';
	public $userTable = 'users';
	public $userTablePk = 'id';

	public function up()
	{
		echo 'assuming your users table is `users` and pk is `id`...';
		// @todo: add dynamic user table name and pk here
		$this->createTable('comments',
			array(
				'id' => 'pk',
				'message' => 'text',
				'userId' => $this->userColumType,
				'createDate' => 'datetime',
				'CONSTRAINT fk_comments_userId FOREIGN KEY (`userId`)
				 REFERENCES `'.$this->userTable.'`(`'.$this->userTablePk.'`) ON DELETE SET NULL ON UPDATE CASCADE',
			),
			($this->dbConnection->driverName=='mysql') ? 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci' : ''
		);
	}

	public function down()
	{
		$this->dropTable('comments');
	}
}
