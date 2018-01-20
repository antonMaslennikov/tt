<?php /* @var $this Controller */ ?>
<?php $this->beginContent('//layouts/main'); ?>
<div class="span-24">
	<div id="content">
		<?php echo $content; ?>
	</div><!-- content -->
</div>
<div class="span-5 last">
	<div id="sidebar">
	
	<div class="portlet" id="yw2">
		
		<? if (Yii::app()->user->role >= 3 || true): ?>
		
		<div class="portlet-decoration">
			<div class="portlet-title">Фильтры</div>
		</div>
		<div class="portlet-content">
		
		<form id="tasksFilters">
		
			<input type="hidden" name="url" value="<?= Yii::app()->createUrl('Tasks/' . Yii::app()->controller->action->id) ?>" />
		
			<ul class="operations" id="yw3">
			
				<? if (Yii::app()->user->role >= 3): ?>
			
				<li style="margin-top:6px">
					
					Ответственный:<br />
					<select name="executor">

						<option value=""></option>

						<? foreach (Users::getUsersList() AS $k => $e): ?>
							
							<option value="<?= $e->id ?>" <? if ($e->id == $_GET['executor'] || $e->id == $_COOKIE['tt_executor']): ?>selected="selected"<? endif; ?>><?= ($e->fio) ? $e->fio : $e->login ?></option>
							
						<? endforeach; ?>
						
					</select>
				</li>
				
				<? endif; ?>
				
				<li style="margin-top:6px">
					
					Автор задачи:<br />
					<select name="author">

						<option value=""></option>

						<? foreach (Users::getUsersList() AS $k => $e): ?>
							
							<option value="<?= $e->id ?>" <? if ($e->id == $_GET['author'] || $e->id == $_COOKIE['author']): ?>selected="selected"<? endif; ?>><?= ($e->fio) ? $e->fio : $e->login ?></option>
							
						<? endforeach; ?>
						
					</select>
				</li>
				<li style="margin:6px 0">
					
					Статус:<br />
					<select name="status">

						<option value=""></option>

						<? foreach (Tasks::$taskStatus AS $k => $e): ?>
							
							<option value="<?= $k ?>" <? if ($k == $_GET['status'] || $k == $_COOKIE['tt_status']): ?>selected="selected"<? endif; ?>><?= $e ?></option>
							
						<? endforeach; ?>
						
					</select>
				
				</li>
			</ul>
			
			</form>
				
		</div>
		
		<? endif; ?>
	</div>
	
	<?php
		$this->beginWidget('zii.widgets.CPortlet', array(
			'title'=>'Operations',
		));
		$this->widget('zii.widgets.CMenu', array(
			'items'=>$this->menu,
			'htmlOptions'=>array('class'=>'operations'),
		));
		$this->endWidget();
	?>
	
	</div><!-- sidebar -->
	
	
	
</div>
<?php $this->endContent(); ?>