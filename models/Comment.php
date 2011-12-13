<?php

/**
 * This is the model class for table "comments".
 *
 * The followings are the available columns in table 'comments':
 * @property integer $id
 * @property string $message
 * @property integer $userId
 *
 * The followings are the available model relations:
 * @property Users $user
 * @property CommentsCommitMap $commentsCommitMap
 */
class Comment extends CActiveRecord
{
	protected $relationType;
	protected $relationKey;


	/**
	 * Returns the static model of the specified AR class.
	 * @return Comment the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'comments';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('userId', 'numerical', 'integerOnly'=>true),
			array('message', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, message, userId', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'user' => array(self::BELONGS_TO, 'User', 'userId'),
			'commit' => array(self::HAS_ONE, 'GitCommit', 'sha'),
		);
	}

	public function setRelation($relation)
	{
		$this->relationType = $relation['type'];
		$this->relationKey  = $relation['key'];
	}

	protected function afterSave()
	{
		switch($this->relationType)
		{
			case 'commit':
				$this->getDbConnection()->createCommand("INSERT INTO comments_commits_map(commentId, commitSha) VALUES (:id, :sha);")
					 ->execute(array(':id' => $this->id, ':sha' => $this->relationKey));
			break;
		}
		parent::afterSave();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'message' => 'Message',
			'userId' => 'User',
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
