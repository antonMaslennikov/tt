<?php
/* @var $this UsersController */
/* @var $model Users */

$this->breadcrumbs=array(
	'Все пользователи'=>array('admin'),
	$model->id=>array('view','id'=>$model->id),
	'редактировать',
);

$this->menu=array(
	array('label'=>'Manage Users', 'url'=>array('admin')),
	array('label'=>'Create Users', 'url'=>array('create')),
);
?>

<h1>Редактировать пользователя #<?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>