<?php
/* @var $this UsersController */
/* @var $model Users */

$this->breadcrumbs=array(
	'Все пользователи'=>array(in_array(Yii::app()->user->role, array(3)) ? 'admin' : 'index'),
	$model->login,
);

$this->menu=array(
	array('label'=>'Все пользователи', 'url'=>array('admin')),
	array('label'=>'Создать нового', 'url'=>array('create')),
	array('label'=>'Редактировать', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Удалить', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
);

if (in_array(Yii::app()->user->role, array(3))) {
    $this->menu=array(
        array('label'=>'Все пользователи', 'url'=>array('admin')),
        array('label'=>'Создать нового', 'url'=>array('create')),
        array('label'=>'Редактировать', 'url'=>array('update', 'id'=>$model->id)),
        array('label'=>'Удалить', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
    );
} else {
    $this->menu=array(
        array('label'=>'Все пользователи', 'url'=>array('index')),
    );
    
    if (Yii::app()->user->id == $model->id) {
        array_push(
            $this->menu,
            array('label'=>'Редактировать', 'url'=>array('update', 'id'=>$model->id))
        );
    }
}
?>

<h1>Пользователь <?php echo $model->login; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'login',
		'email',
		'fio',
		'registration_date_caption',
		array(
			'label'=>'Роль (должность)',
			'type'=>'raw',
			'value'=> Users::$roles[$model->role],
		),
	),
)); 
?>

<br />
<hr />


<?
$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		
		array(              
            'label'=>'Активных задач',
            'type'=>'raw',
            'value'=>CHtml::link(CHtml::encode($model->tasksStatisticks['opened']), array('Tasks/index', 'executor'=>$model->id)),
        ),
		array(              
            'label'=>'Ожидающих проверки',
            'type'=>'raw',
            'value'=>CHtml::link(CHtml::encode($model->tasksStatisticks['waiting']), array('Tasks/index', 'executor'=>$model->id, 'status' => Tasks::STATUS_WAITING)),
        ),
		array(              
            'label'=>'Приостановленных задач',
            'type'=>'raw',
            'value'=>CHtml::link(CHtml::encode($model->tasksStatisticks['paused']), array('Tasks/index', 'executor'=>$model->id, 'status' => Tasks::STATUS_PAUSED)),
        ),
		array(              
            'label'=>'Просроченных задач',
            'type'=>'raw',
            'value'=>CHtml::link(CHtml::encode($model->tasksStatisticks['overdue']), array('Tasks/index', 'executor'=>$model->id, 'status' => Tasks::STATUS_OPENED, 'overdue' => true)),
        ),
	),
));
?>
