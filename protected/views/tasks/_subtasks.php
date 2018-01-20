<li id="subtask-<?= $task->id ?>" style="position:relative">
    <span class="finishTask-block" style="position:relative">
	    <? echo CHtml::checkBox('Tasks[_subtasks][' . $task->id . ']', $task->status == Tasks::STATUS_FINISHED, array('_id' => $task->id, 'class' => 'finishSubtask')); ?>
	    <div class="finishTask-widget">
	        <div class="triangle"></div>
	        <form class="finishTask-form" method="post" action="index.php?r=tasks/finish&id=<?= $task->id ?>">
                <input type="text" name="hour" placeholder="часы">
                <input type="text" name="minute" placeholder="мин">
                <input type="hidden" name="id" value="<?= $task->id ?>">
                <button type="submit">завершить</button>
                <input type="reset" value="отменить">
	        </form>
	    </div>
	</span>
	
	<span class="text <?= $task->status ?>">
		<b><? echo ($task->author->login); ?></b>:
		<? echo ($task->caption_processed) ? $task->caption_processed : $task->caption ?>
	</span>
	
	<?= CHtml::link('', array('tasks/delete', 'id'=>$task->id), array('class' => 'subtask-delete', 'onclick' => 'javascript: return confirm(\'Вы уверены что хотите удалить эту подзадачу?\')')) ?>
	<?= CHtml::link('', array('tasks/update', 'id'=>$task->id), array('class' => 'subtask-update')) ?>
	
	<span class="executors">
		<? 
			$executors = array(); 
			foreach($task->executors as $e) $executors[] = CHtml::link(CHtml::encode($e->login), array('users/view', 'id'=>$e->id)); 
			echo (count($executors) > 0) ? implode(', ', $executors) : ''; 
		?>
	</span>
	
	<? if ($task->spent > 0): ?>
	<span class="task-time-spent" style="position:absolute;left:101%; border: 1px solid #ccc; padding: 2px; background: #f7f7f7; font-size: 10px;white-space:nowrap;">
	    Потрачено: <? if ($task->spent > 0) echo floor($task->spent / 60) . 'ч. ' . ($task->spent % 60); else echo $task->spent; ?> м.
	</span>
	<? endif; ?>
	
	<div class="clr"></div>
</li>
