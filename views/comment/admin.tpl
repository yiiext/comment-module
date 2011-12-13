{$cs=Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('comment-grid', {
		data: $(this).serialize()
	});
	return false;
});
")}

<h1>Manage Comments</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

{CHtml::link('Advanced Search','#', ['class'=>'search-button'])}
<div class="search-form" style="display:none">
{$this->renderPartial('_search', [
	'model'=>$model
])}
</div><!-- search-form -->

{$this->widget('zii.widgets.grid.CGridView', [
	'id'=>'comment-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>[
		'id',
		'message',
		'userId',
		[
			'class'=>'CButtonColumn'
		]
	]
], true)}
