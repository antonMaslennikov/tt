<?php
/* @var $this TasksController */
/* @var $model Tasks */

$this->breadcrumbs=array(
	'Задачи'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Редактировать',
);

$this->menu=array(
	array('label'=>'Все задачи', 'url'=>array('index')),
	array('label'=>'Новая задача', 'url'=>array('create')),
	array('label'=>'Просмотр задачи', 'url'=>array('view', 'id'=>$model->id)),
	//array('label'=>'Управлять задачами', 'url'=>array('admin')),
);
?>

<h1>Редактировать задачу #<?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>