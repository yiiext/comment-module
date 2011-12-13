<div class="view">

	<div style="float: left;">
		{$this->widget('comment.extensions.gravatar.yii-gravatar.YiiGravatar', [
		    'email'=>$data->userEmail,
		    'size'=>80,
		    'defaultImage'=>'monsterid',
		    'secure'=>false,
		    'rating'=>'r',
		    'emailHashed'=>false,
		    'htmlOptions'=>[
		        'alt'=>$data->userName,
		        'title'=>$data->userName
		    ]
		], true)}
	</div>
    <div style="float: left; margin-left: 10px;">

		{CHtml::link(CHtml::encode($data->id), ['view', 'id'=>$data->id])}
		<b>{CHtml::encode($data->getAttributeLabel('userId'))}:</b>
		{CHtml::encode($data->userId)}

		<br />
		<br />
		{CHtml::encode($data->message)}

    </div>

	<br style="clear: both;"/>
</div>
