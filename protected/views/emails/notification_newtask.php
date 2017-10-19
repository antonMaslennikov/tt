<p><? echo $author->login ?> назначил вас ответственным за задачу "<a href="<? echo Yii::app()->createAbsoluteUrl('tasks/view', array('id' => $id)) ?>"><? echo $caption ?></a>".</p>
<p><? echo $text ?></p>
<? if ($deadline): ?><p><b>Крайний срок</b>: <? echo $deadline ?></p><? endif ?>