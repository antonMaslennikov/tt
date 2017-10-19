<?php
/* @var $this TasksController */
/* @var $model Tasks */

$this->breadcrumbs=array(
	'Все задачи'=>array('index'),
	$model->caption,
);

$this->menu=array(
	array('label'=>'Все задачи', 'url'=>array('index')),
	array('label'=>'Новая задача', 'url'=>array('create')),
	array('label'=>'Редактировать', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Добавить подзадачу', 'url'=>array('create', 'parent'=>$model->id)),
	array('label'=> ($model->sticked == 0 ? 'Закрепить задачу' : 'Открепить задачу'), 'url'=>array('stick', 'id'=>$model->id)),
	array('label'=>'Удалить', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Вы уверены что хотите удалить эту задачу?')),
	
);

if ($model->status != 'finished')
{
	array_push($this->menu, array('label'=>'-----------------------------------'));
	array_push($this->menu, array('label'=>'Завершить задачу', 'url'=>'#', 'linkOptions'=>array('submit'=>array('finish','id'=>$model->id),'confirm'=>'Вы уверены что хотите завершить эту задачу?')));
}

?>

<h1>Задача "<?php echo CHtml::encode($model->caption); ?>"</h1>

<?php if(Yii::app()->user->hasFlash('task_finish_error')):?>
    <div class="error-message">
        <?php echo Yii::app()->user->getFlash('task_finish_error'); ?>
    </div>
<?php endif; ?>

<?php 
	
	foreach($model->executors as $e) $executors[] = CHtml::link(CHtml::encode($e->login), array('users/view', 'id'=>$e->id));
	
	$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		array(
			'label'=>'Описание',
			'type'=>'raw',
			'value'=>$model->text,
		),
		array(
			'label'=>'Автор',
			'type'=>'raw',
			'value'=>CHtml::link(CHtml::encode($model->author->login), array('users/view', 'id'=>$model->author->id)),
		),
		'created',
		array(
			'label'=>'Крайний срок',
			'type'=>'raw',
			'value'=>($model->deadline != '0000-00-00') ? $model->deadline . (($model->_overdue) ? '<span class="task-overdue-span ml10">просрочена</span>' : '<span class="ml10">(осталось ' . $model->_toend . ')</span>') : 'не определён',
		),
		array(
			'label'=>'Статус',
			'type'=>'raw',
			'value'=>Tasks::$taskStatus[$model->status],
		),
		array(
			'label'=>'Приоритет',
			'type'=>'raw',
			'value'=>Tasks::$taskPriority[$model->priority],
		),
		array(
			'label'=>'Ответственные',
			'type'=>'raw',
			'value'=> (count($executors) > 0) ? implode(', ', $executors) : 'не назначены',
		),
		
	),
)); ?>

<br /><br />

<div>
	
	<h3>Файлы</h3>
	
	<?php 
		$this->renderPartial('_attachments',array(
			'task'=>$model,
			'attachments'=>$model->attachments,
        )); 
	?>
	
</div>

<br /><br />

<div id="subtasks">

	<? if ($model->status != 'finished' || count($model->_subtasks) > 0): ?>

	<h3>Подзадачи</h3>
	
	<? endif; ?>
		
	<?php 
	
		if ($model->status != 'finished') 
		{
			$this->renderPartial('_form_subtask_v2',array(
				'task'=>$model,
			));
		}
	?>
	
	<? if (count($model->_subtasks)): ?>
	
	<ul class="subtasks">
		<?php
		
			foreach($model->_subtasks as $subtask): 
		
				$this->renderPartial('_subtasks',array(
					'task'=>$subtask
				)); 
			
			endforeach;
		?>
	</ul>
	
	<? endif; ?>

</div>

<br /><br />

<div id="comments">
    <?php if($model->commentCount >= 1): ?>
        <h3>
            Комментариев: <span id="comments-counter"><?php echo $model->commentCount; ?></span>
        </h3>
 
        <?php 
			$this->renderPartial('_comments',array(
				'post'=>$model,
				'comments'=>$model->comments,
			)); 
		?>
    <?php endif; ?>
	
	<h3>Оставить комментарий</h3>
    
	<?php 
		$this->renderPartial('/comment/_form',array(
			'task'=>$model,
			'model'=>$comment,
		)); 
	?>
    
</div>

<?
	Yii::app()->clientScript->registerScriptFile(
		CHtml::asset(Yii::getPathOfAlias('ext.assets.js') . DIRECTORY_SEPARATOR . 'tasks.js'),
		CClientScript::POS_END
	);
?>
