<?php
/* @var $this UsersController */
/* @var $model Users */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'users-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('enctype'=>'multipart/form-data'))); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<table>
	<tr>
		<td style="width:415px">
			<div class="row">
				<?php echo $form->labelEx($model,'login'); ?>
				<?php echo $form->textField($model,'login',array('size'=>60,'maxlength'=>50)); ?>
				<?php echo $form->error($model,'login'); ?>
			</div>
			
			<div class="row">
				<?php echo $form->labelEx($model,'fio'); ?>
				<?php echo $form->textField($model,'fio',array('size'=>60,'maxlength'=>100)); ?>
				<?php echo $form->error($model,'fio'); ?>
			</div>
			
			<div class="row">
				<?php echo $form->labelEx($model,'email'); ?>
				<?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>100)); ?>
				<?php echo $form->error($model,'email'); ?>
			</div>
			
			<?php if (!$model->isNewRecord): ?>
				<div class="row">
					<?php echo $form->labelEx($model,'registration_date'); ?>
					<?php 
						$this->widget('zii.widgets.jui.CJuiDatePicker', array(
							'model' => $model,
							'attribute' => 'registration_date',
							'language' => 'ru',
							'htmlOptions' => array(
								'size' => '15',         // textField size
								'maxlength' => '10',    // textField maxlength
							),
						));
					?>
					<?php echo $form->error($model,'registration_date'); ?>
				</div>
			<?php endif; ?>
			
			<div class="row">
				<?php echo $form->labelEx($model,'avatar'); ?>
				<?php echo $form->fileField($model,'avatar'); echo (strpos($model->avatar, Users::$avatar_def) === false) ? CHtml::link('Удалить',array('deleteAvatar','id'=>$model->id)) : ''; ?>
				<?php echo $form->error($model,'avatar'); ?>
			</div>

			<div class="row">
				<?php echo $form->labelEx($model,'new_password'); ?>
				<?php echo $form->passwordField($model,'new_password',array('value' => '', 'size'=>60,'maxlength'=>100)); ?>
				<?php echo $form->error($model,'new_password'); ?>
			</div>
			
			<div class="row">
				<?php echo $form->labelEx($model,'new_confirm'); ?>
				<?php echo $form->passwordField($model,'new_confirm',array('value' => '', 'size'=>60,'maxlength'=>100)); ?>
				<?php echo $form->error($model,'new_confirm'); ?>
			</div>
			
			<div class="row buttons">
				<?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить'); ?>
				
				<?php if (!$model->isNewRecord): ?>
				<?php echo CHtml::button('Отмена', array('onclick' => "location.href='" . Yii::app()->createUrl('users/view', array('id' => $model->id)) . "'")); ?>
				<?php endif; ?>
			</div>
		</td>
		<td style="vertical-align:top">
		
			<? if (in_array(Yii::app()->user->role, array(3))): ?>
		
			<div class="row">
				<?php echo $form->labelEx($model,'role'); ?>
				<?php echo $form->dropDownList($model,'role', Users::$roles); ?>
				<?php echo $form->error($model,'role'); ?>
			</div>
			
			<? endif; ?>
		</td>
	</tr>
	</table>

<?php  $this->endWidget(); ?>

</div><!-- form -->