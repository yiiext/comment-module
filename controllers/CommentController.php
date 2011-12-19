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
		/** @var Comment $comment */
		$comment = Yii::createComponent($this->module->commentModelClass);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST[$cClass=get_class($comment)]))
		{
			$comment->attributes = $_POST[$cClass];
			$comment->type = $_POST[$cClass]['type'];
			$comment->key  = $_POST[$cClass]['key'];

			// determine current users id
			if (Yii::app()->user->isGuest) {
				$comment->userId = null;
			} else {
				$comment->userId = Yii::app()->user->id;
			}

			if(Yii::app()->request->isAjaxRequest) {
				$output = '';
				if($comment->save())
				{
					// refresh model to replace CDbExpression for timestamp attribute
					$comment->refresh();

					// render new comment
					$output .= $this->renderPartial('_view',array(
						'data'=>$comment,
					), true);

					// create new comment model for empty form
					$comment = Yii::createComponent($this->module->commentModelClass);
					$comment->type = $_POST[$cClass]['type'];
					$comment->key  = $_POST[$cClass]['key'];
				}
				// render comment form
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

		if(isset($_POST[$cClass=get_class($model)]))
		{
			$model->attributes=$_POST[$cClass];
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
	/*public function actionAdmin()
	{
		$model=Yii::createComponent($this->module->commentModelClass, 'search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Comment']))
			$model->attributes=$_GET['Comment'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}*/

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model = Yii::createComponent($this->module->commentModelClass)->findByPk((int) $id);
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
