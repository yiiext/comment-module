Comment module
--------------

makes every entity of your application commentable.
Features:
* Ajax creation of comment
* Gravatar support
* define multiple models that can be commented
* more coming soon...

Resources
---------

* Found a bug or want a feature? [Report it on github](https://github.com/yiiext/comment-module/issues)
* [Code on github](https://github.com/yiiext/comment-module)
* E-Mail the author: CeBe <[mail@cebe.cc](mailto:mail@cebe.cc)>

Quickstart
----------

Go to your application baseDir (`protected` in default yii webapp).
Clone git repo or [download](https://github.com/yiiext/comment-module/tags) latest release.

```sh
git clone https://github.com/yiiext/comment-module.git extensions/comment-module
```
You can also add it to git as a submodule (run command from root of your repo):

```sh
git submodule add https://github.com/yiiext/comment-module.git protected/extensions/comment-module
```

Add module to your application config:

```php
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
            'userEmailAttribute'=>'email';
        ),
        // ...
    ),
    // ...
```

Create database tables:
You can use the database migration provieded by this extension or create a table (example for mysql):

```sql
    CREATE TABLE IF NOT EXISTS `comments` (
      `id`         int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `message`    text COLLATE utf8_unicode_ci,
      `userId`     int(11) UNSIGNED DEFAULT NULL,
      `createDate` datetime DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `fk_comments_userId` (`userId`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
```
You might also want to add a foreign key for `userId` column that references you user tables pk.

Create a database table for every commentable Model relation:

```sql
    CREATE TABLE IF NOT EXISTS `posts_comments_nm` (
      `postId`    int(11) UNSIGNED NOT NULL,
      `commentId` int(11) UNSIGNED NOT NULL,
      PRIMARY KEY (`taskId`,`commentId`),
      KEY `fk_tasks_comments_comments` (`commentId`),
      KEY `fk_tasks_comments_tasks` (`taskId`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
```
You might want to add foreign keys here too.

Add commentable behavior to all Models you want to be commented.

```php
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
```

Finally add comments to your view template of the commentable model:

```php
<h1>comments</h1>

<?php $this->renderPartial('comment.views.comment.commentList', array(
	'model'=>$model
)); ?>
```

If there is something missing here, or you think one step should be described more detailed please [report it](https://github.com/yiiext/comment-module/issues/new). Thanks!
