<h1>View Comment #{$model->id}</h1>

{$this->widget('zii.widgets.CDetailView', [
	'data'=>$model,
	'attributes'=>[
		'id',
		'message',
		'userId'
	]
], true)}
