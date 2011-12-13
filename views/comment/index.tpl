<h1>Comments</h1>

{$this->widget('zii.widgets.CListView', [
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view'
], true)}