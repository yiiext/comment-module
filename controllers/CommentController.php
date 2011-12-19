<?php

/**
 *
 * @property CommentModule $module
 *
 * @author Carsten Brandt <mail@cebe.cc>
 * @package yiiext.modules.comment
 */
class CommentController extends CController
{
	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return $this->module->controllerFilters;
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return $this->module->controllerAccessRules;
	}

	/**
	 * Creates a new comment.
	 *
	 * On Ajax request:
	 *   on successfull creation comment/_view is rendered
	 *   on error comment/_form is rendered
	 * On POST request:
	 *   If creation is successful, the browser will be redirected to the
	 *   url specified by POST value 'returnUrl'.
	 */
	public function actionCreate()
	{
		$comment = new Comment();

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Comment']))
		{
			$comment->attributes = $_POST['Comment'];
			$comment->type = $_POST['Comment']['type'];
			$comment->key = $_POST['Comment']['key'];

			// determine current users id
			if (Yii::app()->user->isGuest) {
				$comment->userId = null;
			} else {
				$comment->userId = Yii::app()->user->id;
			}

			if(Yii::app()->request->isAjaxRequest) {
				$output = '';
				if($comment->save()) {
					$comment->refresh(); // need this to replace CDbExpression for timestamp attribute
					$output .= $this->renderPartial('_view',array(
						'data'=>$comment,
					), true);
					$comment = new Comment();
					$comment->type = $_POST['Comment']['type'];
					$comment->key = $_POST['Comment']['key'];
				}
				$output .= $this->renderPartial('_form',array(
					'comment'=>$comment,
					'ajaxId'=>time(),
				), true);
				// render javascript functions
				Yii::app()->clientScript->renderBodyEnd($output);
				echo $output;
				Yii::app()->end();
			} else {
				if($comment->save()) {
					$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('view','id'=>$comment->id));
				} else {
					// @todo: what if save fails?
				}
			}
		}

		$this->render('create',array(
			'model'=>$comment,
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
		$model = Comment::model()->findByPk((int) $id);
		if ($model === null) {
			throw new CHttpException(404,'The requested page does not exist.');
		}
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
