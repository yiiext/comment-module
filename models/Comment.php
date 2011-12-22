<?php

/**
 * This is the model class for table "comments".
 *
 * @property-read CommentModule $module the comment module
 * @property string $type this is set to one of the commentableModels scope from CommentModule
 * @property mixed  $key the primary key of the AR this comment belongs to
 *
 * The followings are the available columns in table 'comments':
 * @property integer $id
 * @property string  $message
 * @property integer $userId
 * @property integer $createDate
 *
 * The followings are the available model relations:
 * @property Users $user
 * @property CommentsCommitMap $commentsCommitMap
 *
 * @author Carsten Brandt <mail@cebe.cc>
 * @package yiiext.modules.comment
 */
class Comment extends CActiveRecord
{
	private $_type;
	private $_key;
	private $_new = false;

	/**
	 * @var string set the commentableModels scope from CommentModule
	 */
	public function setType($type)
	{
		$this->_type = strtolower($type);
	}

	/**
	 * @return string get the comments scope
	 */
	public function getType()
	{
		return $this->_type;
	}

	/**
	 * @var mixed set the primary key of the AR this comment belongs to
	 */
	public function setKey($key)
	{
		$this->_key = $key;
	}

	/**
	 * @return mixed the primary key of the AR this comment belongs to
	 */
	public function getKey()
	{
		return $this->_key;
	}

	/**
	 * Returns the static model of the specified AR class.
	 * @return Comment the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return CommentModule the comment module instance
	 */
	public function getModule()
	{
		return Yii::app()->getModule('comment');
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'comments';
	}

	public function behaviors()
	{
		return array(
			'CTimestampBehavior' => array(
				'class' => 'zii.behaviors.CTimestampBehavior',
				'createAttribute' => 'createDate',
				'updateAttribute' => null,
				// need special DbExpression when db is sqlite
				'timestampExpression' => (strncasecmp('sqlite', $this->dbConnection->driverName, 6)===0) ?
					new CDbExpression("datetime('now')") : null,
			),
		);
	}
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('message', 'safe'),
			array('type', 'validateType', 'on'=>'create'),
			array('key',  'validateKey',  'on'=>'create'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, message, userId', 'safe', 'on'=>'search'),
		);
	}

	public function validateType()
	{
		if (!isset($this->module->commentableModels[$this->type])) {
			throw new CException('comment type ' . $this->type . ' not defined in CommentModule!');
		}
	}

	public function validateKey()
	{
		$commentableModel = CActiveRecord::model($this->module->commentableModels[$this->type]);
		if ($commentableModel->asa('commentable') === null) {
			throw new CException('commentable Model must have behavior CommentableBehavior attached!');
		}
		if ($commentableModel->findByPk($this->key) === null) {
			throw new CException('comment related record does not exist!');
		}
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'user' => array(self::BELONGS_TO, $this->module->userModelClass, 'userId'),
		);
	}

	protected function beforeSave()
	{
		$this->_new = $this->isNewRecord;
		return parent::beforeSave();
	}

	protected function afterSave()
	{
		if ($this->_new) {
			$commentedModel = CActiveRecord::model($this->module->commentableModels[$this->type]);
			// if comment is new, connect it with commended model
			$this->getDbConnection()->createCommand(
				"INSERT INTO ".$commentedModel->mapTable."(".$commentedModel->mapCommentColumn.", ".$commentedModel->mapRelatedColumn.")
				 VALUES (:id, :key);"
			)->execute(array(':id' => $this->id, ':key' => $this->key));

			parent::afterSave();

			// raise new comment event
			$this->module->onNewComment($this, $commentedModel->findByPk($this->key));
		} else {
			parent::afterSave();
		}
		// raise update comment event
		$this->module->onUpdateComment($this/*, $commentedModel->findByPk($this->key)*/);
	}

	protected function afterDelete()
	{
		parent::afterDelete();
		// raise update comment event
		$this->module->onDeleteComment(
			$this/*,
			CActiveRecord::model($this->module->commentableModels[$this->type])->findByPk($this->key)*/
		);
	}

	/**
	 * @return string get comment users name
	 */
	public function getUserName()
	{
		return is_null($this->user) ? 'Guest' : $this->user->{$this->module->userNameAttribute};
	}

	/**
	 * @return string get comment users email
	 */
	public function getUserEmail()
	{
		return is_null($this->user) ? 'nobody@example.com' : $this->user->{$this->module->userEmailAttribute};
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'message' => 'Message',
			'userId' => 'User ID',
			'userName' => 'Name',
			'userEmail' => 'E-Mail',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('message',$this->message,true);
		$criteria->compare('userId',$this->userId);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
