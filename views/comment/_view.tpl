<div class="view">

	<div style="float: left;">
		{$this->widget('ext.gravatar.yii-gravatar.YiiGravatar', [
		    'email'=>$data->user->email,
		    'size'=>80,
		    'defaultImage'=>'monsterid',
		    'secure'=>false,
		    'rating'=>'r',
		    'emailHashed'=>false,
		    'htmlOptions'=>[
		        'alt'=>$data->user->displayName,
		        'title'=>$data->user->displayName
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
