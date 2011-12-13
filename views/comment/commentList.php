<?php

/** @var CArrayDataProvider $comments */
$comments = $model->getCommentDataProvider();
$comments->setPagination(false);

$this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$comments,
	'itemView'=>'comment.views.comment._view'
));

$this->renderPartial('comment.views.comment._form', array(
	'comment'=>$model->commentInstance
));
