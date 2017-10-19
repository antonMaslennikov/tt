<?php

/**
 * This is the model class for table "tt_comments".
 *
 * The followings are the available columns in table 'tt_comments':
 * @property integer $id
 * @property integer $task_id
 * @property integer $user_id
 * @property string $text
 */
class Comment extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tt_comments';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('task_id, user_id, text', 'required'),
			array('task_id, user_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, task_id, user_id, text, date', 'safe', 'on'=>'search'),
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
			'author' => array(self::BELONGS_TO, 'Users', 'user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'task_id' => 'Task',
			'user_id' => 'User',
			'text' => 'Text',
			'date' => 'Date',
		);
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

		$criteria->compare('id',$this->id);
		$criteria->compare('task_id',$this->task_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('text',$this->text,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Comment the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	
	protected function afterSave()
	{
		$task = Tasks::model()->findByPk($this->task_id);
		$user = Users::model()->findByPk($this->user_id);
		
		if (count($task->executors) > 0)
		{
			$email = new YiiMailMessage;
			$email->view = 'notification_newcomment';
			$email->subject = 'Новый комментарий к задаче';
			$email->from = Yii::app()->params['adminEmail'];
			
			$i = 0;

			foreach ($task->executors AS $uid => $v)
			{
				if ($this->user_id != $v->id) {
					$email->addTo(Users::model()->findByPk($v->id)->email);
					$i++;
				}
			}
			
			if ($i > 0)
			{
				$email->setBody(array(
							'id' => $task->id, 
							'caption' => $task->caption, 
							'text' => $this->text, 
							'login' => $user->login
						), 
						'text/html');
				
				Yii::app()->mail->send($email);
			}
		}
	}
}
