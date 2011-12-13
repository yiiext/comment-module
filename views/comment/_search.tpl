<div class="wide form">

{$form=$this->beginWidget('CActiveForm', [
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get'
])}

	<div class="row">
		{$form->label($model,'id')}
		{$form->textField($model,'id')}
	</div>

	<div class="row">
		{$form->label($model,'message')}
		{$form->textArea($model,'message',['rows'=>6, 'cols'=>50])}
	</div>

	<div class="row">
		{$form->label($model,'userId')}
		{$form->textField($model,'userId')}
	</div>

	<div class="row buttons">
		{CHtml::submitButton('Search')}
	</div>

{$end=$this->endWidget()}

</div><!-- search-form -->