<p>Пользователь <? echo $user ?> завершил <? if ($task->parent > 0): ?>подзадачу<? else: ?>задачу<? endif; ?> "<a href="<? echo Yii::app()->createAbsoluteUrl('tasks/view', array('id' => $task->id)) ?>"><? echo $task->caption ?></a>".</p>
<? if ($task->parent > 0): ?>
<p>
	Задача "<a href="<? echo Yii::app()->createAbsoluteUrl('tasks/view', array('id' => $parent->id)) ?>"><? echo $parent->caption ?></a>"
</p>
<? endif; ?>