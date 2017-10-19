<div style="margin-bottom:25px;margin-left:20px;width: 880px;">

	<a href="#" class="showSubtaskForm dashed" title="добавить подзадачу">добавить подзадачу</a>
	
	<?php
		$new = new Tasks;
		
		$form=$this->beginWidget('CActiveForm', array(
			'id'=>'mini-subtask-form',
			'enableAjaxValidation'=>false,
			'enableClientValidation'=>false,
		)); 
	?>
	
		
		<table width="100%">
		<tr>
			<td>
				<?php echo $form->textField($new,'caption', array('size'=>109, 'placeholder' => 'суть подзадачи')); ?>
				<?php echo $form->error($new,'caption'); ?>
			</td>
		
			<td width="100">
				<div id="executors-widget">
				
					<a href="#" class="select-executors dashed">исполнители</a>
					
					<ul>
						<div class="triangle"></div>
					
						<? foreach ($task->_subexecutors AS $e): ?>
							
							<? if ($e->id == Yii::app()->user->id) $author_paresed = true; ?>
							
							<li><label><input type="checkbox" name="Tasks[_executors][<? echo $e->id ?>]" value="<? echo $e->id ?>" /> <? echo $e->login ?></label></li>
							
						<? endforeach; ?>
						
					</ul>
					
					<input type="hidden" name="executors" />
				</div>
			</td>
			
			<td width="100" style="text-align:right">
				<? echo CHtml::ajaxSubmitButton('Добавить', Yii::app()->createUrl('tasks/create', array('parent' => $task->id)), array(
					
					'beforeSend'=>'js:function(){
						if ($(\'input#Tasks_caption\').val().length == 0)
						{
							alert(\'Текст подзадачи не может быть пуст\');
							return false;
						}
					}',
					'error'=>'js:function(){
						alert(\'error: \' + data);
					}',
					'success'=>'js:function(data){
						$(\'ul.subtasks\').prepend(data);
						$(\'input#Tasks_caption\').val(\'\');
						$(\'.showSubtaskForm\').text($(\'.showSubtaskForm\').attr(\'title\'));
						$(\'#mini-subtask-form, #executors-widget > ul \').hide();
					}',
				
				)); ?>
			</td>
			
		</tr>
		</table>
	
	<?php $this->endWidget(); ?>
		
</div>