<p>Пользователь <? echo $login ?> добавил новый комментарий к задаче "<a href="<? echo Yii::app()->createAbsoluteUrl('tasks/view', array('id' => $id)) ?>"><? echo $caption ?></a>".</p>

<p><blockquote><? echo $text ?></blockquote></p>