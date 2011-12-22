<?php

/**
 * Add this behavior to AR Models that are commentable
 * You have to create mapping Table to build the relation in
 * your database. A migration for creating such a table could look like this:
 * <pre>
 * class m111212_030738_add_comment_task_relation extends CDbMigration
 * {
 *     public function up()
 *     {
 *         $this->createTable('tasks_comments_nm', array(
 *             'taskId' => 'bigint(20) unsigned NOT NULL',
 *              'commentId' => 'int',
 *              'PRIMARY KEY(taskId, commentId)',
 *              'KEY `fk_tasks_comments_comments` (`commentId`)',
 *              'KEY `fk_tasks_comments_tasks` (`taskId`)',
 *         ), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
 *
 *         $this->addForeignKey('fk_tasks_comments_comments', 'tasks_comments_nm', 'commentId', 'comments', 'id', 'CASCADE', 'CASCADE');
 *         $this->addForeignKey('fk_tasks_comments_tasks', 'tasks_comments_nm', 'taskId', 'tasks', 'id', 'CASCADE', 'CASCADE');
 *     }
 *
 *     public function down()
 *     {
 *         $this->dropTable('tasks_comments_nm');
 *     }
 * }
 * </pre>
 * In behavio config you have to set {@see $mapTable} to the name of the table
 * and {@see $mapCommentColumn} and {@see $mapRelatedColumn} to the column names you chose.
 * <pre>
 * public function behaviors() {
 *     return array(
 *         'commentable' => array(
 *              'class' => 'ext.comment-module.behaviors.CommentableBehavior',
 *              'mapTable' => 'tasks_comments_nm',
 *              'mapRelatedColumn' => 'taskId'
 *              'mapCommentColumn' => 'commentId'
 *          ),
 *     );
 * }
 * </pre>
 *
 * @property CommentModule $module
 *
 * @author Carsten Brandt <mail@cebe.cc>
 * @package yiiext.modules.comment
 */
class CommentableBehavior extends CActiveRecordBehavior
{
	/**
	 * @var string name of the table defining the relation with comment and model
	 */
	public $mapTable = null;
	/**
	 * @var string name of the table column holding commentId in mapTable
	 */
	public $mapCommentColumn = 'commentId';
	/**
	 * @var string name of the table column holding related Objects Id in mapTable
	 */
	public $mapRelatedColumn = null;

	public function attach($owner)
	{
		parent::attach($owner);
		// make sure comment module is loaded so views can be rendered properly
		Yii::app()->getModule('comment');
	}

	/**
	 * @return CommentModule
	 */
	public function getModule()
	{
		return Yii::app()->getModule('comment');
	}

	/**
	 * returns a new comment instance that is related to the model this behavior is attached to
	 *
	 * @return Comment
	 * @throws CException
	 */
	public function getCommentInstance()
	{
		$comment = Yii::createComponent($this->module->commentModelClass);
		$types = array_flip($this->module->commentableModels);
		if (!isset($types[$c=get_class($this->owner)])) {
			throw new CException('No scope defined in CommentModule for commentable Model ' . $c);
		}
		$comment->setType($types[$c]);
		$comment->setKey($this->owner->primaryKey);
		return $comment;
	}

	/**
	 * get all related comments for the model this behavior is attached to
	 *
	 * @return Comment[]
	 * @throws CException
	 */
	public function getComments()
	{
		$comments = Yii::createComponent($this->module->commentModelClass)
					     ->findAll($this->getCommentCriteria());
		// get model type
		$type = get_class($this->owner);
		foreach($this->module->commentableModels as $scope => $model) {
			if ($type == $model) {
				$type = $scope;
				break;
			}
		}
		foreach($comments as $comment) {
			/** @var Comment $comment */
			$comment->setType($type);
			$comment->setKey($this->owner->primaryKey);
		}
		return $comments;
	}

	/**
	 * count all related comments for the model this behavior is attached to
	 *
	 * @return int
	 * @throws CException
	 */
	public function getCommentCount()
	{
		return Yii::createComponent($this->module->commentModelClass)
					->count($this->getCommentCriteria());
	}

	protected function getCommentCriteria()
	{
		if (is_null($this->mapTable) || is_null($this->mapRelatedColumn)) {
			throw new CException('mapTable and mapRelatedColumn must not be null!');
		}

		// @todo: add support for composite pks
		return new CDbCriteria(array(
			'join' => "JOIN " . $this->mapTable . " cm ON t.id = cm." . $this->mapCommentColumn,
		    'condition' => "cm." . $this->mapRelatedColumn . "=:pk",
			'params' => array(':pk'=>$this->owner->getPrimaryKey())
		));
	}

	/**
	 * @todo this should be moved to a controller or widget
	 *
	 * @return CArrayDataProvider
	 */
	public function getCommentDataProvider()
	{
		return new CArrayDataProvider($this->getComments());
	}
}
