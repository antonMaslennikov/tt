<?php
/* @var $this TasksController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Задачи',
);

$this->menu=array(
	array('label'=>'Новая задача', 'url'=>array('create')),
	//array('label'=>'Управлять задачами', 'url'=>array('admin')),
);

Yii::app()->clientScript->registerScriptFile(
	CHtml::asset(Yii::getPathOfAlias('ext.assets.js') . DIRECTORY_SEPARATOR . 'tasks.js'),
	CClientScript::POS_END
);

Yii::app()->clientScript->registerScriptFile(
	CHtml::asset(Yii::getPathOfAlias('ext.assets.js') . DIRECTORY_SEPARATOR . 'jquery.cookie.js'),
	CClientScript::POS_END
);

?>

<h1>

	<? if (Yii::app()->controller->action->id == 'index'): ?>
		Задачи
	<? else: ?>
		<a href="<?= $this->createUrl('tasks/index') ?>">Задачи</a>
	<? endif; ?>
	/ 
	<? if (Yii::app()->controller->action->id == 'subtasks'): ?>
		Подзадачи
	<? else: ?>
		<a href="<?= $this->createUrl('tasks/subtasks') ?>">Подзадачи</a>
	<? endif; ?>
	
</h1>

<?php /*$this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
	'sortableAttributes'=>array(
		'priority'=>'Приоритет',
		'deadline' => 'Крайний срок',
	),
));*/ ?>

<?php 

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'tasks-grid',
	'dataProvider'=>$dataProvider,
	'rowCssClassExpression' => function($row, $data) {
		if ($data->sticked == 1) {
            return 'tr_class_sticked';
        }
		else {
			if ($row % 2 == 0)
				return 'even';
			else
				return 'odd';
		}
    },
	
	'columns'=>array(
		array(
			'header' => '№',
			'name' => 'id',
			'value' => '$row+1',
		),
		array(
			'name' => 'caption',
			'type'=>'raw',
			'value' => 'CHtml::link($data->caption, array("view", "id"=>$data->id))',
			'htmlOptions' =>array('style' => 'font-size:16px', 'background'),
		),
		array(
			'name' => 'priority',
			'value' => 'Tasks::$taskPriority[$data->priority]',
		),
		array(
			'name' => 'deadline',
			'value' => '$data->_deadline_rus',
		),
		array(
			'name' => 'status',
			'value' => 'Tasks::$taskStatus[$data->status]',
		),
		array(
			'name' => 'subtasks_opened',
			'value' => '$data->subtasks_opened',
		),
		
	),
	
)); ?>
