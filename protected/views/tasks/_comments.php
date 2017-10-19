<ul class="comments">

	<?php foreach($comments as $comment): ?>

		<li id="comment-<?= $comment->id ?>">
			<table>
			<tr>
				<td width="30"><img src="<?= $comment->author->avatar ?>" alt="<?= $data->login ?>" /></td>
				<td>
					<p><b><?php echo CHtml::link(CHtml::encode($comment->author->login), array('users/view', 'id'=>$comment->author->id)) ?></b> <em><?php echo $comment->date ;?></em>:</p>
					<div><?php echo $comment->text; ?></div>
				</td>
				<td width="80" class="td" style="vertical-align:bottom">
					<? if ($comment->user_id == Yii::app()->user->id): ?>
						<small class="small"><?php echo CHtml::ajaxLink('ред.', array('comment/view', 'id'=>$comment->id, 'ajax' => true), array('type'=>'POST', 'success'=>'js:function(r){ var data = eval(\'(\' + r + \')\'); $(\'#comment-form\').find(\'textarea\').val(data.text); $(\'#comment-form\').find(\'input#Comment_id\').val(' . $comment->id . '); }')) ?></small>
						|
						<small class="small"><?php echo CHtml::ajaxLink('удалить', array('comment/delete', 'id'=>$comment->id), array('type'=>'POST', 'success'=>'js:function(data){ $(\'#comment-' . $comment->id . '\').remove(); $(\'#comments-counter\').text(parseInt($(\'#comments-counter\').text()) - 1) }')) ?></small>
					<? endif ?>
				</td>
			</tr>
			</table>
		</li>

	<?php endforeach; ?>

</ul>