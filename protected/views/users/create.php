<?php
/* @var $this UsersController */
/* @var $model Users */

$this->breadcrumbs=array(
	'Все пользователи'=>array('admin'),
	'Создать',
);

$this->menu=array(
	array('label'=>'Manage Users', 'url'=>array('admin')),
);
?>

<h1>Добавить нового пользователя</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>