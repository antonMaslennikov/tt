<?php

class TasksController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			//'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('index','view','create','update','finish','attach','delete','subtasks'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','stick'),
				'roles'=>array(2, 3),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$task = $this->loadModel($id);
		$comment = $this->newComment($task);

		$this->render('view',array(
			'model'=>$task,
			'comment'=>$comment,
		));
	}
	
	protected function newComment($task)
	{
		if (empty($_POST['Comment']['id']))
			$comment=new Comment;
		else
			$comment = Comment::model()->findByPk($_POST['Comment']['id']);
		
		// ajax-валидация формы добавления камента
		if(isset($_POST['ajax']) && $_POST['ajax']==='comment-form')
		{
			echo CActiveForm::validate($comment);
			Yii::app()->end();
		}
		
		if(isset($_POST['Comment']))
		{		
			$comment->attributes=$_POST['Comment'];
			
			if($task->addComment($comment))
			{
				$this->refresh();
			}
		}
		
		return $comment;
	}
	

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate($parent = 0)
	{
		$model=new Tasks;

		if (!empty($parent))
			$model->parent = $parent;
		
		// Uncomment the following line if AJAX validation is needed
		//$this->performAjaxValidation($model);

		if(isset($_POST['Tasks']))
		{
			$model->attributes=$_POST['Tasks'];
			
			if($model->save()) 
			{
				if (Yii::app()->request->isAjaxRequest)
				{
					$model->linkDetect();
					
					$this->renderPartial('_subtasks',array(
						'task'=>$model,
					)); 
					exit();
				}
				else
					$this->redirect(array('view','id'=> (!empty($parent)) ? $model->parent : $model->id));
			}
			else
			{
				if (Yii::app()->request->isAjaxRequest) {
					header('HTTP/1.0 500 Error');
					exit(json_encode($model->errors));
				}
			}
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);
		
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Tasks']))
		{
			$model->attributes=$_POST['Tasks'];
			
			if (count($model->_executors) > 0)
			{
				// вычисляем список новых исполнителей задачи
				foreach($model->executors AS $e) {
					$executors[] = $e->id;
				}
				
				$model->new_executors = array_diff(array_keys((array) $model->_executors), (array) $executors);
			}
			
			if($model->save())
				$this->redirect(array('view','id'=> (!empty($model->parent)) ? $model->parent : $model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}
	
	public function actionStick($id)
	{
		$model=$this->loadModel($id);
		
		$model->sticked= ($model->sticked == 0) ? 1 : 0;
		
		$model->setScenario('stick');
		
		if($model->save()) {
			if(!isset($_GET['ajax']))
				$this->redirect(array('view','id'=> (!empty($model->parent)) ? $model->parent : $model->id));
		}
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id=NULL)
	{
		$model = $this->loadModel($id);
		$model->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			if (!empty($model->parent))
				$this->redirect(array('view','id'=>$model->parent));
			else
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
	}

	/**
	 * Lists all models.
	 * @param $executor int ответственный
	 * @param $author int автор задачи
	 * @param $status string(array) статус задачи
	 */
	public function actionIndex($executor = null, $status = null, $author = null, $overdue = null)
	{
		$this->layout='//layouts/tasks.index';
		
		$criteria = new CDbCriteria; 
		
		$criteria->alias = 'tasks';
		
		$criteria->select = 'tasks.*, COUNT(DISTINCT(sub_tasks.`id`)) AS subtasks_opened';
		
		$criteria->compare('tasks.parent', 0);
	
		if (empty($status) && !empty(Yii::app()->request->cookies['tt_status']->value))
			$status = Yii::app()->request->cookies['tt_status']->value;

		if (empty($executor) && !empty(Yii::app()->request->cookies['tt_executor']->value))
			$executor = Yii::app()->request->cookies['tt_executor']->value;
		
		if (empty($author) && !empty(Yii::app()->request->cookies['tt_author']->value))
			$author = Yii::app()->request->cookies['tt_author']->value;
		
		// фильтрация по статусу задачи
		if ($status)
			$criteria->addInCondition('tasks.status', (array) $status);
		else
			$criteria->addNotInCondition('tasks.status', (array) Tasks::STATUS_FINISHED);
		
		if ($overdue)
			$criteria->addCondition("tasks.deadline <= NOW() AND tasks.deadline != '0000-00-00'");
		
		// фильтрация по исполнителю
		if ($executor)
		{
			$criteria->with = array('executors' => array('condition' => 'executors.id = ' . $executor));
		}

		// фильтрация по автору
		if ($author)
		{
			$criteria->compare('tasks.author_id', $author);
		}
		
		$criteria->join = 'LEFT JOIN `tt_tasks` sub_tasks ON tasks.`id` = sub_tasks.`parent` AND sub_tasks.`status` = "opened"';

		// сортировка
		$criteria->order = "tasks.`sticked` DESC";
		
		if (!isset($_GET['Tasks_sort']))
		{
			//$criteria->order .= ", IF(tasks.`deadline` !=  '0000-00-00' AND NOW() >= tasks.`deadline`, 1, 0) DESC, tasks.deadline DESC";
			$criteria->order .= ", IF(tasks.`deadline` !=  '0000-00-00', 1, 0) DESC, tasks.deadline ASC";
		}
		
		$criteria->together = true;
		
		$criteria->group = 'tasks.`id`';
		
		$dataProvider=new CActiveDataProvider('Tasks', array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize'=>100,
            ),
		));

		$this->render('index',array(
			'dataProvider'=>$dataProvider
		));
	}
	
	public function actionSubtasks($executor = null, $status = null, $author = null, $overdue = null)
	{
		$this->layout='//layouts/tasks.index';
		
		$criteria = new CDbCriteria; 
		
		$criteria->alias = 'tasks';
		
		$criteria->select = 'tasks.*, parent.`caption` AS parent_caption, parent.`deadline` AS parent_deadline, parent.`sticked` AS parent_sticked';
		
		$criteria->compare('tasks.parent', '> 0');
	
		if (empty($status) && !empty(Yii::app()->request->cookies['tt_status']->value))
			$status = Yii::app()->request->cookies['tt_status']->value;

		if (empty($executor) && !empty(Yii::app()->request->cookies['tt_executor']->value))
			$executor = Yii::app()->request->cookies['tt_executor']->value;
		
		if (empty($author) && !empty(Yii::app()->request->cookies['tt_author']->value))
			$author = Yii::app()->request->cookies['tt_author']->value;
		
		// фильтрация по статусу задачи
		if ($status)
			$criteria->addInCondition('tasks.status', (array) $status);
		else
			$criteria->addNotInCondition('tasks.status', (array) Tasks::STATUS_FINISHED);
		
		if ($overdue)
			$criteria->addCondition("tasks.deadline <= NOW() AND tasks.deadline != '0000-00-00'");
		
		// фильтрация по исполнителю
		if ($executor)
		{
			$criteria->with = array('executors' => array('condition' => 'executors.id = ' . $executor));
		}

		// фильтрация по автору
		if ($author)
		{
			$criteria->compare('tasks.author_id', $author);
		}
		
		$criteria->join = 'LEFT JOIN `tt_tasks` parent ON tasks.`parent` = parent.`id`';

		// сортировка
		$criteria->order = "parent.`sticked` DESC";
		
		if (!isset($_GET['Tasks_sort']))
		{
			//$criteria->order .= ", IF(tasks.`deadline` !=  '0000-00-00' AND NOW() >= tasks.`deadline`, 1, 0) DESC, tasks.deadline DESC";
			$criteria->order .= ", IF(parent.`deadline` !=  '0000-00-00', 1, 0) DESC, parent.`deadline` ASC";
		}
		
		$criteria->together = true;
		
		$criteria->group = 'tasks.`id`';
		
		$dataProvider=new CActiveDataProvider('Tasks', array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize'=>100,
            ),
		));

		$this->render('subtasks',array(
			'dataProvider'=>$dataProvider
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Tasks('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Tasks']))
			$model->attributes=$_GET['Tasks'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}
	
	/**
	 * завершить задачу
	 */
	function actionFinish($id)
	{
		$model = $this->loadModel($id);
		
		try
		{
			$model->finish();
		}
		catch (exception $e) 
		{
			Yii::app()->user->setFlash('task_finish_error', $e->getMessage()); 
		}
		
		if(Yii::app()->request->isAjaxRequest) {
			Yii::app()->end();
		} else {
			$this->redirect(array('view','id'=>$model->id));
		}
	}
	
	/**
	 * загрузить и прикрепить к задаче файл
	 */
	function actionAttach($id)
	{
		$model = $this->loadModel($id);
		
		$f = CUploadedFile::getInstanceByName('file');
		
		$ext  = strtolower(end(explode('.', $f->name)));
		$path = DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, array(date('Y'),date('m'),date('d'))) . DIRECTORY_SEPARATOR . substr(md5(uniqid()), 0, 10) . '_' . $f->name;
		
		File::createDir($path);
	
		try
		{
			$f->saveAs(Yii::getPathOfAlias('webroot') . $path);
		
			$file = new File;
			$file->path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
			$file->mime = $f->type;
			$file->extention = $ext;
			$file->size = $f->size;
			$file->save();
		}
		catch (exception $e) { exit($e->getMessage()); }
		
		Yii::app()->db
			->createCommand()
			->insert('tt_tasks_attachments', array(
					'task_id'=>$model->id,
					'file_id'=>$file->id,));
					
		$this->redirect(array('view','id'=>$model->id));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Tasks the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Tasks::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Tasks $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='tasks-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
