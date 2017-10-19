<?php
/* @var $this TasksController */
/* @var $data Tasks */
?>

<table class="view priority-<?= $data->priority ?> <? echo ($data->_overdue) ? 'task-overdue' : '' ?>">
<tr>
	
	<td class="task-number">
		<?php echo $index+1; ?>
	</td>

	<td class="task-description">
		<?php echo CHtml::link(CHtml::encode($data->caption), array('view', 'id'=>$data->id)); ?>
	</td>
	
	<td class="task-options">
		<?php echo CHtml::encode(Tasks::$taskPriority[$data->priority]); ?>
	</td>
	
	<td class="task-options">	
		<span class="deadline"><?php echo ($data->deadline == '0000-00-00') ? 'не определён' : CHtml::encode($data->deadline); ?></span>
	</td>
	
	<td class="task-options">
		<?php echo CHtml::encode(Tasks::$taskStatus[$data->status]); ?>
	</td>
	
</tr>
</table>
	