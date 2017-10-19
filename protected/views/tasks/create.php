<?php
/* @var $this TasksController */
/* @var $model Tasks */

$this->breadcrumbs=array(
	'Все задачи'=>array('index'),
	'Создать',
);

$this->menu=array(
	array('label'=>'Все задачи', 'url'=>array('index')),
	//array('label'=>'Управлять задачами', 'url'=>array('admin')),
);
?>

<h1>Создать 
		<? echo (empty($model->parent)) ? 'задачу' : 'подзадачу'; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>