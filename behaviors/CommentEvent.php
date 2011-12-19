<?php

/**
 *
 * @author Carsten Brandt <mail@cebe.cc>
 * @package yiiext.modules.comment
 */
class CommentEvent extends CEvent
{
	/**
	 * @var Comment the comment related to this event
	 */
	public $comment = null;

	/**
	 * @var CActiveRecord the commented object if available
	 */
	public $commentedModel = null;
}
