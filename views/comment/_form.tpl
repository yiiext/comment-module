<div id="comment-form-ajax" class="form">

{$form=$this->beginWidget('CActiveForm', [
	'id'=>'comment-form',
    'action'=>['/comment/comment/create'],
	'enableAjaxValidation'=>false
])}

	{$form->error($comment,'userId')}

	<div class="row">
		{$form->labelEx($comment,'message')}
		{$form->textArea($comment,'message',['rows'=>6, 'cols'=>50])}
		{$form->error($comment,'message')}
	</div>

	{$form->hiddenField($comment, 'type')}
    {$form->hiddenField($comment, 'key')}

	<div class="row buttons">
	    {if $comment->isNewRecord}
            {* CHtml::hiddenField('returnUrl', $this->createUrl(''))}
		    {CHtml::submitButton('Save') *}
		    {CHtml::ajaxSubmitButton('Submit',
                ['/comment/comment/create'],
		        [
                    'replace'=>'#comment-form-ajax',
                    'error'=>"function(){
                        $('#Comment_message').css('border-color', 'red');
                        $('#Comment_message').css('background-color', '#fcc');
                    }"
		        ],
		        ['id'=>'submit-comment'|cat:($ajaxId|default:"")]
		    )}
		{else}
		    {CHtml::submitButton('Save')}
		{/if}
	</div>

{$end=$this->endWidget()}

</div><!-- form -->
