<?php
/* @var $this UsersController */
/* @var $model Users */

$this->breadcrumbs=array(
	'Все пользователи'=>array(in_array(Yii::app()->user->role, array(3)) ? 'admin' : 'index'),
	$model->id=>array('view','id'=>$model->id),
	'редактировать',
);

if (in_array(Yii::app()->user->role, array(3))) {
    $this->menu=array(
        array('label'=>'Все пользователи', 'url'=>array('admin')),
        array('label'=>'Создать нового', 'url'=>array('create')),
        array('label'=>'Удалить', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
    );
} else {
    $this->menu=array(
        array('label'=>'Все пользователи', 'url'=>array('index')),
    );
}
?>

<h1>Редактировать пользователя #<?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>