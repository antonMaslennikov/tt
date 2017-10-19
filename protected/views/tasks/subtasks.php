<?php
/* @var $this TasksController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Подзадачи',
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

<?php 

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'tasks-grid',
	'dataProvider'=>$dataProvider,
	'rowCssClassExpression' => function($row, $data) {
		if ($data->parent_sticked == 1) {
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
			'name' => 'author',
			'value' => '$data->author->login',
		),
		array(
			'name' => 'caption',
			'value' => '$data->caption',
			'htmlOptions' =>array('style' => 'font-size:16px', 'background'),
		),
		array(
			'name' => 'parent_caption',
			'type'=>'raw',
			'value' => 'CHtml::link($data->parent_caption, array("view", "id"=>$data->parent))',
		),
		array(
			'name' => 'parent_deadline',
			'value' => '$data->parent_deadline',
		),
		array(
			'header' => 'Исполнители',
			'name' => 'executors',
			'type'=>'raw',
			'value' => '$data->executorsString()',
		),
	),
	
)); ?>
