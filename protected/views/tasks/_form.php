<?php
/* @var $this TasksController */
/* @var $model Tasks */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'tasks-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model); ?>

	<br />
	
	<div class="row">
	
		<?php echo $form->labelEx($model,'caption'); ?>
		<?php echo $form->textField($model,'caption',array('size'=>109,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'caption'); ?>
	
	</div>
	
	<?php if (empty($model->parent)): ?>
	
		<div class="row">
			<?php echo $form->labelEx($model,'text'); ?>
			<?php 
				$this->widget('application.extensions.ckeditor.CKEditor', array(
					'model'=>$model,
					'attribute'=>'text',
					'language'=>'ru',
					'editorTemplate'=>'full',
					'options' => array(
						'height' =>"150px",
						'width'  =>"95%",
						'toolbar'=>"Basic",
					),
				));
			?>
			<?php echo $form->error($model,'text'); ?>
			
			<br />
		</div>
		
	<?php endif; ?>
		
	<table>
	<tr>
		
		<td width="200" style="vertical-align:top">
			<div class="row">
				<label>Ответственные:</label>
				<ul id="executorsList">
					
					<? $executors = array(); ?>
					
					<? foreach($model->executors AS $e) $executors[$e->id] = 1; ?>
					
					<?
						if ($model->parent)
						{
							$connection=Yii::app()->db;
		
							$execs = $connection->createCommand()
									->select(array('u.id', 'u.login'))
									->from('tt_users u, tt_tasks_executors te')
									->where('te.task_id=:id and u.id = te.user_id', array(':id'=>$model->parent))
									->setFetchMode(PDO::FETCH_OBJ)	
									->queryAll();
						}
						else
							$execs = Users::getUsersList();
					?>
					
					<?php foreach ($execs AS $uid => $u):  ?>
					<?php echo '<li>' . CHtml::checkBox('Tasks[_executors][' . $u->id . ']', $executors[$u->id]) . ' ' . CHtml::encode($u->login) . '</li>'; ?>
					<?php endforeach; ?>
				</ul>
			</div>
		</td>
		
		<td style="width:200px;vertical-align:top">	
		
			<?php if (empty($model->parent)): ?>
		
				<div class="row">
					<?php echo $form->labelEx($model,'deadline'); ?>
					<?php 
						$this->widget('zii.widgets.jui.CJuiDatePicker', array(
							'model' => $model,
							'attribute' => 'deadline',
							'language' => 'ru',
							'htmlOptions' => array(
								'size' => '15',         // textField size
								'maxlength' => '10',    // textField maxlength
							),
						));
					?>
					<?php echo $form->error($model,'deadline'); ?>
				</div>
				
			<?php endif; ?>
		</td>
			
		<td style="vertical-align:top">			
		
			<?php if (empty($model->parent)): ?>
		
				<div class="row">
					<?php echo $form->labelEx($model,'priority'); ?>
					<?php echo $form->dropDownList($model,'priority', Tasks::$taskPriority); ?>
					<?php echo $form->error($model,'priority'); ?>
				</div>
				
				<div class="row">
					<?php echo $form->labelEx($model,'status'); ?>
					<?php echo $form->dropDownList($model,'status', Tasks::$taskStatus); ?>
					<?php echo $form->error($model,'status'); ?>
				</div>
			
			<?php endif; ?>
		</td>
	</tr>
	</table>
	
	<br />

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить'); ?>
		
		<?php if (!empty($model->parent)): ?>
		
			<?php echo CHtml::button('Отмена', array('onclick' => "location.href='" . Yii::app()->createUrl('tasks/view', array('id' => $model->parent)) . "'")); ?>
		
		<?php endif; ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->