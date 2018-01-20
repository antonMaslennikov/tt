<?php
/* @var $this UsersController */
/* @var $model Users */

$this->breadcrumbs=array(
	'Все пользователи',
);

$this->menu=array(
	array('label'=>'Создать нового', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#users-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Пользователи</h1>

<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'users-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		array(
            'name' => 'avatar',
            'type' => 'image',
            'value' => '$data->avatar',
        ),
		'login',
		'fio',
		'email',
		'registration_date_caption',
		array(
            'name'=>'role',
            'type'=>'raw',
			'value' => 'Users::$roles[$data->role]',
        ),
		array(
            'name'=>'_tasks_active',
            'type'=>'raw',
			'value' => 'Tasks::tastsCount($data->id, ' .  Tasks::STATUS_OPENED . ')',
        ),
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
