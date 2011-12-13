<?php

class CommentController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
        $model = $this->loadModel($id);

        $this->breadcrumbs=array(
            'Comments'=>array('index'),
            $model->id,
        );

        $this->menu=array(
            array('label'=>'List Comment', 'url'=>array('index')),
            array('label'=>'Create Comment', 'url'=>array('create')),
            array('label'=>'Update Comment', 'url'=>array('update', 'id'=>$model->id)),
            array('label'=>'Delete Comment', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
            array('label'=>'Manage Comment', 'url'=>array('admin')),
        );

		$this->render('view',array(
			'model'=>$model,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Comment;

/*        $this->breadcrumbs=array(
            'Comments'=>array('index'),
            'Create',
        );

        $this->menu=array(
            array('label'=>'List Comment', 'url'=>array('index')),
            array('label'=>'Manage Comment', 'url'=>array('admin')),
        );*/

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);


		if(isset($_POST['Comment']))
		{
			$model->attributes=$_POST['Comment'];
			$model->setRelation($_POST['CommentRelation']);
			$model->userId = 1;
			if(Yii::app()->request->isAjaxRequest) {
				$output = '';
				if($model->save()) {
					$output .= $this->renderPartial('_view',array(
						'data'=>$model,
					), true);
					$model = new Comment();
				}
				$output .= $this->renderPartial('_form',array(
					'comment'=>$model,
					'ajaxId'=>time(),
					'relation'=>$_POST['CommentRelation'], // @todo: check input!
				), true);
				Yii::app()->clientScript->renderBodyEnd($output);
				echo $output;
				Yii::app()->end();
			} else {
				if($model->save()) {
					$this->redirect(array('view','id'=>$model->id));
				}
			}
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

        $this->breadcrumbs=array(
            'Comments'=>array('index'),
            $model->id=>array('view','id'=>$model->id),
            'Update',
        );

        $this->menu=array(
            array('label'=>'List Comment', 'url'=>array('index')),
            array('label'=>'Create Comment', 'url'=>array('create')),
            array('label'=>'View Comment', 'url'=>array('view', 'id'=>$model->id)),
            array('label'=>'Manage Comment', 'url'=>array('admin')),
        );

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Comment']))
		{
			$model->attributes=$_POST['Comment'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
        $this->breadcrumbs=array(
            'Comments',
        );

        $this->menu=array(
            array('label'=>'Create Comment', 'url'=>array('create')),
            array('label'=>'Manage Comment', 'url'=>array('admin')),
        );

		$dataProvider=new CActiveDataProvider('Comment');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
        $this->breadcrumbs=array(
            'Comments'=>array('index'),
            'Manage',
        );

        $this->menu=array(
            array('label'=>'List Comment', 'url'=>array('index')),
            array('label'=>'Create Comment', 'url'=>array('create')),
        );

		$model=new Comment('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Comment']))
			$model->attributes=$_GET['Comment'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Comment::model()->findByPk((int)$id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='comment-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
