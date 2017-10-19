<li>
	<? echo CHtml::checkBox('Tasks[_subtasks][' . $task->id . ']', $task->status == Tasks::STATUS_FINISHED, array('_id' => $task->id)); ?>
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
	
	<div class="clr"></div>
</li>
