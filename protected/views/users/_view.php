<?php
/* @var $this UsersController */
/* @var $data Users */
?>

<div class="view users-index">

	<table>
	<tr>
		<td width="30"><img src="<?= $data->avatar ?>" alt="<?= $data->login ?>" /></td>
		<td>
			<?php echo CHtml::link(CHtml::encode($data->login), array('view', 'id'=>$data->id)); ?>
			<br />
			<?= (!empty($data->fio)) ? CHtml::encode($data->fio) : ''?>	(<?= Users::$roles[$data->role] ?>)
		</td>
	</tr>
	</table>
	
</div>