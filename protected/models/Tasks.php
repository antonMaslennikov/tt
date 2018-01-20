<?php

/**
 * This is the model class for table "tt_tasks".
 *
 * The followings are the available columns in table 'tt_tasks':
 * @property string $id
 * @property string $text
 * @property integer $author_id
 * @property string $created
 * @property string $deadline
 */
class Tasks extends CActiveRecord
{	
	public $_executors;
	// ответственные за подзадачи
	public $_subexecutors;
	public $_subtasks = array();
	
	// признак что задача просрочена
	public $_overdue;
	// признак что задача просрочена
	public $_deadline_rus;
	//  сколько осталось до дедлайна в днях и часах
	public $_toend;
	// текст ссылки с заменёными ссылками
	public $caption_processed;
	
	public $subtasks_opened = '-';
	
	public $parent_caption = '-';
	public $parent_deadline = '-';
	public $parent_sticked = '';
	
	public $new_executors = array();
	
	public static $link_chars = "a-zA-Z0-9АаБбВвГгДдЕеЁёЖжЗзИиЙйКкЛлМмНнОоПпРрСсТтУуФфХхЦцЧчШшЩщЪъЫыЬьЭэЮюЯя\%\.\_\,\;\/\-\#\:\?=\&";
	
	/**
	 * @var string для временного хранения статуса задачи при её сохранении для записи в блог при смене статуса
	 */
	private $_status = '';
	
	const STATUS_OPENED   = 'opened';
	const STATUS_FINISHED = 'finished';
	const STATUS_WAITING  = 'waiting';
	const STATUS_PAUSED   = 'paused';
	
	/**
	 * @param array возможные статусы задачи
	 * порядок расположения важен - 
	 * первый присваивается задачам по умолчанию
	 */
	public static $taskStatus = array(
		Tasks::STATUS_OPENED   => 'открыта', 
		Tasks::STATUS_FINISHED => 'завершена', 
		Tasks::STATUS_WAITING  => 'ожидает проверки', 
		Tasks::STATUS_PAUSED   => 'приостановлена',
	);
	
	
	/**
	 * @param array возможные приоритеты задачи
	 * порядок расположения важен - 
	 * первый присваивается задачам по умолчанию
	 */
	public static $taskPriority = array(
		'normal' => 'обычный', 
		'medium' => 'средний', 
		'higher' => 'высокий',
	);
	
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tt_tasks';
	}
	

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('caption', 'required'),
			//array('caption', 'length', 'max'=>255),
			array('deadline', 'length', 'max'=>50),
			array('parent', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, text, author_id, created, deadline, sticked,subtasks_opened,spent', 'safe', 'on'=>'search'),
			array('_executors, text, priority, status, sticked, subtasks_opened', 'safe'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'author'       => array(self::BELONGS_TO, 'Users', 'author_id'),
			'executors'    => array(self::MANY_MANY, 'Users', 'tt_tasks_executors(task_id, user_id)'),
			'attachments'  => array(self::MANY_MANY, 'File', 'tt_tasks_attachments(task_id, file_id)'),
			'comments'     => array(self::HAS_MANY, 'Comment', 'task_id', 'order'=>'comments.date DESC'),
			'log'          => array(self::HAS_MANY, 'Log', 'task_id'),
			'commentCount' => array(self::STAT, 'Comment', 'task_id'),
			);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'caption' => 'Суть',
			'text' => 'Описание',
			'author' => 'Автор',
			'author_id' => 'Id автора',
			'created' => 'Дата создания',
			'deadline' => 'Крайний срок',
			'_deadline_rus' => 'Крайний срок',
			'status' => 'Статус',
			'priority' => 'Приоритет',
			'sticked' => 'Закреплён',
            'spent' => 'Потрачено времени на выполнение',
			'subtasks_opened' => 'Незакрытых задач',
			'parent_caption' => 'Родительская задача',
			'parent_deadline' => 'Крайний срок',
			'_executors' => 'Исполнители',
		);
	}
	
	public function executorsString()
	{
		$connection=Yii::app()->db;
		
		$foo = $connection->createCommand()		
				->select('u.login')
				->from('tt_users u, tt_tasks_executors te')
				->where(array('and', 'te.task_id=:id', 'te.user_id = u.id'), array(':id'=>$this->id))
				;
		foreach ($foo->queryAll() AS $e) {
			$executors[] = $e['login'];
		}
		
		return implode(', ', (array) $executors);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('text',$this->text,true);
		$criteria->compare('author_id',$this->author_id);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('deadline',$this->deadline,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Tasks the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	//public function behaviors()
	//{
	//	return array('CAdvancedArBehavior' => array(
	//		'class' => 'application.extensions.CAdvancedArBehavior'));
	//}
	
	
	/**
	 * добавить комментарий
	 */
	public function addComment($comment)
	{
		$comment->task_id = $this->id;
		$comment->user_id = Yii::app()->user->id;
		$comment->text = nl2br($comment->text);
		
		return $comment->save();
	}
	
	
	/**
	 * @return кол-во заданий на пользователе определённого статуса
	 */
	public static function tastsCount($uid, $status)
	{
		$connection=Yii::app()->db;
		
		$foo = $connection->createCommand()		
				->select('COUNT(t.id) AS c')
				->from('tt_tasks t')
				->join('tt_tasks_executors te', 't.id = te.task_id')
				->where(array('and', 'te.user_id=:id', 't.status=:status', 't.parent=0'), array(':id'=>$uid, ':status' => $status))
				->queryRow();
				
		return (int) $foo['c'];
	}
	
	/**
	 * Удалить всех отвественных с задачи
	 */
	public function removeAllExecutors()
	{
		TasksExecutors::model()->deleteAll("task_id=:task_id", array('task_id' => $this->id));
	}
	
	/**
	 * Проверка является ли пользователь исполнителем задачи
	 * @param (int) $uid 
	 */
	function isExecutor($uid)
	{
		$exec = false;
		
		foreach ($this->executors as $e) {
			if ($e->id == $uid) {
				$exec = true;
				break;
			}
		}
		
		return $exec;
	}
	
	/**
	 * Завершить задачу
	 */
	function finish()
	{
		if (Yii::app()->user->id != $this->author_id && !$this->isExecutor(Yii::app()->user->id)) {
			throw new Exception("Вы не можете завершить эту задачу", 1);
		}
		
		if ($this->status == self::STATUS_FINISHED) {
		//	throw new Exception("Задача уже завершена", 1);
		    $this->status = self::STATUS_OPENED;
		} else {
            $this->status = self::STATUS_FINISHED;
        }
        
        // запоминаем время выполнения если такие данные пришли
        // так не канает так как при сохранении дорфига операций с исполнителями
        //$this->spent += (int) $_POST['hour'] * 60 + (int) $_POST['minute'];
        //$this->save();
        
		$connection=Yii::app()->db;
		
		// закрываем саму задачу
		$connection->createCommand()		
				->update('tt_tasks', array('status' => $this->status, 'spent' => $this->spent + ((int) $_POST['hour'] * 60 + (int) $_POST['minute'])), 'id=:id', array(':id' => $this->id));
		
		// закрываем все подзадачи
		if (empty($this->parent) && $this->status == self::STATUS_FINISHED)
		{
			$connection->createCommand()		
				->update('tt_tasks', array('status' => self::STATUS_FINISHED), 'parent=:parent', array(':parent' => $this->id));
		}
		
		if (!empty($this->parent)) {
			$parent = Tasks::model()->findByPk($this->parent);
		}
		
		$l = new Log;
			
		$l->task_id = $this->id;
		$l->user_id = Yii::app()->user->id;
		$l->action = 'change_status';
		$l->result = $this->status;
        $l->info = (int) $_POST['hour'] * 60 + (int) $_POST['minute'];
		$l->save();
		
		if (Yii::app()->user->id != $this->author_id && $this->status == self::STATUS_FINISHED)
		{
			/*
			$email = new YiiMailMessage;
			$email->view = 'task.finish';
			$email->subject = 'Завершена задача "' . $this->caption . '"';
			$email->from = Yii::app()->params['adminEmail'];
			
			$email->setBody(array(
					'id' => $this->id, 
					'parent' => $parent,
					'caption' => $this->caption, 
					'task' => $this,
					'user' => Users::model()->findByPk(Yii::app()->user->id)->login,
				), 
				'text/html');
			
			$email->addTo(Users::model()->findByPk($this->author_id)->email);
			
			Yii::app()->mail->send($email);
			*/

			$mail = new YiiMailer();
			$mail->setFrom(Yii::app()->params['adminEmail']);
			$mail->setTo(Users::model()->findByPk($this->author_id)->email);
			$mail->setSubject('Завершена задача "' . $this->caption . '"');
			$mail->setView('task.finish');
			$mail->setData(array(
					'id' => $this->id, 
					'parent' => $parent,
					'caption' => $this->caption, 
					'task' => $this,
					'user' => Users::model()->findByPk(Yii::app()->user->id)->login,
				));
			
			$mail->send();
		}
        else
        {
            // todo :: уведомление о том что задача возобновлена
            
        }
		
		return $this->status;
	}
	
	function linkDetect()
	{
		$this->caption_processed = preg_replace("'<a[^>]*?>(.*?)</a>'si",'\\1', $this->caption); // заменить ссылку в тегах - на ссылку без тегов
		$this->caption_processed = preg_replace("#(https://|http://|www.)[" . self::$link_chars . "]*#",'<a href="\\0" rel="nofollow" target="_blank">\\0</a>', $this->caption_processed); // превращаем урлы в ХТМЛ ссылки
	}
	
	
	public function afterFind()
	{
		// если это задача первого уровня
		if (empty($this->parent))
		{
			// извлекаем список подзадач
			$this->_subtasks = Tasks::model()->findAll(array(
					'condition' => 'parent=:parent', 
					'order' => 'status DESC, created DESC', 
					'params' => array(':parent' => $this->id)));
			
			// отмечаем просроченные задачи
			if ($this->deadline != '0000-00-00')
			{
				if (strtotime($this->deadline) < time())
					$this->_overdue = true;
				else {
					$time  = strtotime($this->deadline) - time();
					
					$days  = floor($time / (3600 * 24));
					$hours = floor(($time - ($days * 3600 * 24)) / 3600);
					
					$this->_toend = (($days > 0) ? $days . ' д. ' : '') . $hours . ' ч.';
				}
				
				$this->_deadline_rus = $this->deadline;
			}
			else
			{
				$this->_deadline_rus = 'не определён';
			}
			
			/**
			 * формируем список ответственных за подзадачи
			 * из собственно говоря назначенных ответственных и автора задачи 
			 */
			foreach ($this->executors AS $e)
				if ($e->id == $this->author_id) $author_paresed = true;
			
			$this->_subexecutors = $this->executors;
			
			if (!isset($author_paresed)) {
				$this->_subexecutors = array_merge(array(0 => Users::model()->findByPk($this->author_id)), $this->_subexecutors);
			}
		}
		else
		{
			// в подзадачах урлы заменяем на ссылки
			if (!empty($this->parent))
			{
				$this->linkDetect();
			}
		}
		
		return $this->_subtasks;
	}
	
	protected function beforeSave()
	{
		if(parent::beforeSave())
		{
			if (!$this->author_id)
				$this->author_id=Yii::app()->user->id;

			if (!$this->status)
				$this->status=reset(array_keys(self::$taskStatus));
				
			if (!$this->priority)
				$this->priority=reset(array_keys(self::$taskPriority));
			
			if ($this->deadline && $this->deadline != '0000-00-00')
				$this->deadline = date('Y-m-d', strtotime($this->deadline));
			
			return true;
		}
		else
			return false;
	}
	
	protected function afterSave()
	{
		if ($this->getScenario() == 'stick')  {
			return true;
		}
		
		if ($this->parent == 0)
		{
			$l = new Log;
			
			$l->task_id = $this->id;
			$l->user_id = Yii::app()->user->id;
		}	
		
		$email = new YiiMailMessage;
		$email->view = ($this->parent == 0) ? 'notification_newtask' : 'notification_newsubtask';
		$email->subject = ($this->parent == 0) ? 'Вас назначили ответственными за задачу "' . $this->caption . '"' : 'Вас назначили ответственными за подзадачу к задаче "' . Tasks::model()->findByPk($this->parent)->caption . '"';
		$email->from = Yii::app()->params['adminEmail'];
		
		if (!$this->isNewRecord)
		{
			$this->removeAllExecutors();
			
			/*
			if ($this->status != $this->_status)
			{
				$l->action = 'change_status';
				$l->result = $this->status;
			}
			*/

		}
		else
		{
			if ($this->parent == 0)
			{
				$l->action = 'create';
			}
			
			
		}
		
		if ($this->parent == 0)
		{
			$l->save();
		}
		
		if (count($this->_executors) > 0)
		{
			$i = 0;
			
			foreach ($this->_executors AS $uid => $v)
			{
				$exec = new TasksExecutors();			
				$exec->task_id = $this->id;
				$exec->user_id = $uid;
				$exec->save();
				
				// email уведомление о новой задаче
				if ($this->isNewRecord)
				{
					if ($this->author_id != $uid) 
					{
						$email->addTo(Users::model()->findByPk($uid)->email);
						$i++;
					}
				}
				// уведомление о назначении на вас существующей задачи
				else
				{
					if (in_array($uid, $this->new_executors) && $uid != Yii::app()->user->id) {
						$email->addTo(Users::model()->findByPk($uid)->email);
						$i++;
					}
				}
			}
			
			if ($i > 0)
			{
				$email->setBody(array(
						'id' => $this->id, 
						'parent' => $this->parent,
						'caption' => $this->caption, 
						'text' => $this->text, 
						'deadline' => $this->deadline,
						'author' => Users::model()->findByPk(Yii::app()->user->id),
					), 
					'text/html');
				
					
				Yii::app()->mail->send($email);
			}
		}
		
		return parent::afterSave();
	}
}
