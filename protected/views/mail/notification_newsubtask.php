<p><? echo $author->login ?> назначил вас ответственным за подзадачу</p>
<p>"<? echo $caption ?>"</p>
<p>к задаче"<a href="<? echo Yii::app()->createAbsoluteUrl('tasks/view', array('id' => $parent)) ?>"><? echo Tasks::model()->findByPk($parent)->caption ?></a>".</p>
