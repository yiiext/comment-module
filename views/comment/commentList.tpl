{$comments->setPagination(false)}
{$this->widget('zii.widgets.CListView', [
	'dataProvider'=>$comments,
	'itemView'=>'//comment/_view'
], true)}

{$this->renderPartial('//comment/_form', [
	'comment'=>$comment,
	'relation'=>$relation
])}
