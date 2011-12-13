{$comments = $model->getCommentDataProvider()}
{$comments->setPagination(false)}
{$this->widget('zii.widgets.CListView', [
	'dataProvider'=>$comments,
	'itemView'=>'comment.views.comment._view'
], true)}

{$this->renderPartial('comment.views.comment._form', [
	'comment'=>$model->commentInstance
])}
