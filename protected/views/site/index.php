<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

<? 
	if (Yii::app()->user->isGuest)
	{
		$this->redirect(array('/site/login'));
	}
	else
	{
	?>
	
		Здесь мы сделаем такой крутой дашборд для активного юзера :)
	
	<?
	}
	
?>
