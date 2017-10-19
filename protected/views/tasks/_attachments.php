<ul id="attachments">

	<li>
		<a href="#" class="showUploadForm dashed">добавить файл</a>
			
		<div class="hidden">
			<?php echo CHtml::form(Yii::app()->createUrl('tasks/attach', array('id' => $task->id)),'post',array('enctype'=>'multipart/form-data')); ?>
			<?php echo CHtml::fileField('file'); ?>
			<?php echo CHtml::submitButton('загрузить'); ?>
			<?php echo CHtml::endForm(); ?>
		</div>
	</li>

	<?php foreach($attachments as $file): ?>

		<li class="exts ext-<? echo $file->extention ?>" id="file-<? echo $file->id ?>">
			<div class="left">
				<? echo CHtml::link($file->real_filename, $file->path, array('target' => '_blank')) ?>&nbsp;&nbsp;&nbsp;
				<em style="color:#ccc">(<? echo $file->size_rounded ?>)</em>
			</div>
			<div class="right">
				<? if ($file->user_id == Yii::app()->user->id): ?>
					<? echo CHtml::ajaxLink('удалить', array('file/delete', 'id' => $file->id, 'ajax' => true), array(
							'type' => 'POST',
							'success' => 'js:function(data){ $("li#file-' . $file->id . '").remove(); }',
							'error' => 'js:function(data){ alert(\'Ошибка удаления файла\') }'
						)) ?>
				<? endif; ?>
			</div>
			<div class="clr"></div>
		</li>
	
	<?php endforeach; ?>
	
</ul>