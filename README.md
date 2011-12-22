Comment module
--------------

makes every entity of your application commentable.
Features:

* Create, Update, Delete comments with ajax
* Gravatar support
* define multiple models that can be commented
* Events raised on new, update, delete
* more coming soon...

If there is something missing here, or you think one step should be described more detailed,
please [report it](https://github.com/yiiext/comment-module/issues/new). Thanks!

Requirements
------------

* Tested with Yii 1.1.8, should work in earlier versions
* [yii-gravatar extension](https://github.com/malyshev/yii-gravatar) for gravatar support

Resources
---------

* Found a bug or want a feature? [Report it on github](https://github.com/yiiext/comment-module/issues)
* [Code on github](https://github.com/yiiext/comment-module)
* E-Mail the author: CeBe <[mail@cebe.cc](mailto:mail@cebe.cc)>
* demo on [http://demo.cebe.cc/comment-module](http://demo.cebe.cc/comment-module)

Download
--------

There are two ways to get this extension working:

1. Clone repo:
   * Go to your application baseDir (`protected` in default yii webapp).
   * `git clone https://github.com/yiiext/comment-module.git extensions/comment-module`
     * If your project is in a git repository you can alternatively add comment-module as a submodule like this:
     * `git submodule add https://github.com/yiiext/comment-module.git protected/extensions/comment-module`
   * go to new comment-modules base dir and run
     `git submodule update --init` to get the gravatar extension that's included.

2. [Download](https://github.com/yiiext/comment-module/tags) latest release and put all the files into
   `extensions/comment-module` under your application baseDir (`protected` in default yii webapp).
   To be able to use Gravatar support you have to copy [YiiGravatar.php](https://github.com/malyshev/yii-gravatar/tree/master/yii-gravatar)
   into `extensions/comment-module/extensions/gravatar`.

Quickstart
----------

Add module to your application config (optional config values are commented):

~~~php
<?php
    // ...
    'modules'=>array(
        // ...
        'comment'=>array(
            'class'=>'ext.comment-module.CommentModule',
            'commentableModels'=>array(
                // define commentable Models here (key is an alias that must be lower case, value is the model class name)
                'post'=>'Post'
            ),
            // set this to the class name of the model that represents your users
            'userModelClass'=>'User',
            // set this to the username attribute of User model class
            'userNameAttribute'=>'username',
            // set this to the email attribute of User model class
            'userEmailAttribute'=>'email',
            // you can set controller filters that will be added to the comment controller {@see CController::filters()}
//          'controllerFilters'=>array(),
            // you can set accessRules that will be added to the comment controller {@see CController::accessRules()}
//          'controllerAccessRules'=>array(),
            // you can extend comment class and use your extended one, set path alias here
//	        'commentModelClass'=>'comment.models.Comment',
        ),
        // ...
    ),
    // ...
~~~

Create database tables:
You can use the database migration provieded by this extension or create a table (example for mysql):

~~~sql
    CREATE TABLE IF NOT EXISTS `comments` (
      `id`         int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `message`    text COLLATE utf8_unicode_ci,
      `userId`     int(11) UNSIGNED DEFAULT NULL,
      `createDate` datetime DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `fk_comments_userId` (`userId`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
~~~
You might also want to add a foreign key for `userId` column that references you user tables pk.

Create a database table for every commentable Model relation:

~~~sql
    CREATE TABLE IF NOT EXISTS `posts_comments_nm` (
      `postId`    int(11) UNSIGNED NOT NULL,
      `commentId` int(11) UNSIGNED NOT NULL,
      PRIMARY KEY (`postId`,`commentId`),
      KEY `fk_posts_comments_comments` (`commentId`),
      KEY `fk_posts_comments_posts` (`postId`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
~~~
You might want to add foreign keys here too.

Add commentable behavior to all Models you want to be commented.

~~~php
<?php
    // ...
    public function behaviors() {
        return array(
            'commentable' => array(
                'class' => 'ext.comment-module.behaviors.CommentableBehavior',
                // name of the table created in last step
                'mapTable' => 'posts_comments_nm',
                // name of column to related model id in mapTable
                'mapRelatedColumn' => 'postId'
            ),
       );
    }
~~~

Finally add comments to your view template of the commentable model:

~~~php
<h1>comments</h1>

<?php $this->renderPartial('comment.views.comment.commentList', array(
	'model'=>$model
)); ?>
~~~


Extending Comment-Module
------------------------

Comment module raises [events](http://www.yiiframework.com/doc/guide/1.1/en/basics.component#component-event)
to which you can attach event handlers to handle them.
See [The Definitive Guide to Yii](http://www.yiiframework.com/doc/guide/1.1/en/basics.component#component-event) on how to do this.

You can also attach [behaviors](http://www.yiiframework.com/doc/guide/1.1/en/basics.component#component-behavior)
to CommentModule by setting `'behaviors'=>array(/* ... */)` in the module config described above.
See [CModule::behaviors](http://www.yiiframework.com/doc/api/1.1/CModule#behaviors-detail) on how to add behaviors to a module.

### onNewComment

This event is raised when a new comment has been saved.
The following attributes are available on the `$event` given as the first parameter to the event handler:

* `$event->comment` is the ActiveRecord instance of the currently added comment.
* `$event->commentedModel` is the model the comment was added to.

Possible use cases:

* Send an E-Mail-Notification

### onUpdateComment

This event is raised when a user edited a comment.
The following attributes are available on the `$event` given as the first parameter to the event handler:

* `$event->comment` is the ActiveRecord instance of the updated comment.

### onDeleteComment

This event is raised when a user deleted a comment.
The following attributes are available on the `$event` given as the first parameter to the event handler:

* `$event->comment` is the ActiveRecord instance of the deleted comment.

