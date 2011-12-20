<?php if (Yii::app()->user->isGuest) {
?><div class="comment-not-loggedin">
	Sorry, you have to login to leave a comment.
</div><?php } else { ?>
<div id="comment-form-ajax" class="form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id'=>'comment-form',
    'action'=>array('/comment/comment/create'),
	'enableAjaxValidation'=>false
)); ?>

	<?php echo $form->error($comment,'userId'); ?>

	<div class="row">
		<?php echo $form->labelEx($comment,'message'); ?>
		<?php echo $form->textArea($comment,'message',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($comment,'message'); ?>
	</div>

	<?php echo $form->hiddenField($comment, 'type'); ?>
	<?php echo $form->hiddenField($comment, 'key'); ?>

	<div class="row buttons">
	    <?php if ($comment->isNewRecord) {
            /* echo CHtml::hiddenField('returnUrl', $this->createUrl(''));}
		    echo CHtml::submitButton('Save'); */
			echo CHtml::ajaxSubmitButton('Submit',
                array('/comment/comment/create'),
		        array(
                    'replace'=>'#comment-form-ajax',
                    'error'=>"function(){
                        $('#Comment_message').css('border-color', 'red');
                        $('#Comment_message').css('background-color', '#fcc');
                    }"
		        ),
		        array('id'=>'submit-comment' . (isset($ajaxId) ? $ajaxId : ''))
		    );
			} else {
		        echo CHtml::submitButton('Save');
			}
		?>
	</div>

<?php $this->endWidget() ?>

</div><!-- form -->
<?php } ?>