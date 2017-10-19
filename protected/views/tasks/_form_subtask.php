<div style="margin-bottom:25px;margin-left:20px;width: 880px;">

	<a href="#" class="showSubtaskForm dashed" title="добавить подзадачу">добавить подзадачу</a>
		
	<?php echo CHtml::form(Yii::app()->createUrl('tasks/create', array('parent' => $task->id)), 'post', array('id' => 'mini-subtask-form')); ?>
		
		<table width="100%">
		<tr>
			<td>
				<?php echo CHtml::textField('Tasks[caption]', '', array('placeholder' => 'суть подзадачи')); ?>
			</td>
		
			<td width="100">
				<div id="executors-widget">
				
					<a href="#" class="select-executors dashed">исполнители</a>
					
					<ul>
						<div class="triangle"></div>
					
						<? foreach ($task->executors AS $e): ?>
							
							<li><label><input type="checkbox" name="Tasks[_executors][<? echo $e->id ?>]" value="<? echo $e->id ?>" /> <? echo $e->login ?></label></li>
							
						<? endforeach; ?>
					</ul>
					
					<input type="hidden" name="executors" />
				</div>
			</td>
			
			<td width="100" style="text-align:right">
				<? echo CHtml::ajaxSubmitButton('Добавить', Yii::app()->createUrl('tasks/create'), array(
					
					'beforeSend'=>'js:function(){
						if ($(\'input#Tasks_caption\').val().length == 0)
						{
							alert(\'Текст подзадачи не может быть пуст\');
							return false;
						}
					}',
					
					'success'=>'js:function(data){
						if (data == \'ok\')
						{
							alert(\'saved\');
						}
						else
							alert(\'success, data from server: \'+data);
					}',
				
				)); ?>
			</td>
			
		</tr>
		</table>
	
	<?php echo CHtml::endForm(); ?>
		
</div>