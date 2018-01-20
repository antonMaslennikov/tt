<?php

/**
 * This is the model class for table "tt_users".
 *
 * The followings are the available columns in table 'tt_users':
 * @property integer $id
 * @property string $login
 * @property string $password
 */
class Users extends CActiveRecord
{
	public $new_password;
	public $new_confirm;

	public $avatar;
	public static $avatar_ext = 'gif';
	public static $avatar_def = '/images/noavatar.gif';
	
	public $registration_date_caption;
	
	/**
	 * @var Количество заданий на пользователе по статусам
	 */ 
	public $tasksStatisticks;
	
	public $_tasks_active;
	
	public static $roles = array(
		0 => 'Рядовой сотрудник',
		1 => 'Менеджер',
		2 => 'Старший менеджер',
		3 => 'Админ',
        4 => 'Программист',
        5 => 'UI',
        6 => 'Системный инженер',
        7 => 'Владелец компании',
	);


	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tt_users';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('login, email', 'required'),
			array('login, role', 'length', 'max'=>50),
			array('fio, email', 'length', 'max'=>100),
			array('registration_date', 'length', 'max'=>50),
			array('email', 'email', 'message'=>'Неверный формат E-mail адреса'),
			array('login', 'unique', 'caseSensitive'=>false, 'message'=>'Данный Логин уже использует другой пользователь'),
			array('email', 'unique', 'caseSensitive'=>false, 'message'=>'Данный E-mail уже использует другой пользователь'),
			
			array('new_password', 'length', 'min'=>4, 'allowEmpty'=>true),
			array('new_password', 'required', 'on'=>'register'),
			array('new_confirm', 'compare', 'compareAttribute'=>'new_password', 'message'=>'Пароли не совпадают'),
			
			array('avatar', 'file', 'types'=>'jpg, gif, png', 'maxSize' => 1048576, 'allowEmpty'=>'true',),
			
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, login, password, fio, email', 'safe', 'on'=>'search'),
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
			'tasks'=>array(self::MANY_MANY, 'Tasks', 'tt_tasks_executors(user_id, task_id)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'       => 'ID',
			'login'    => 'Логин',
			
			'password'     => 'Пароль',
			'new_password' => 'Пароль',
			'new_confirm'  => 'Подтвердите пароль',

			'fio'      => 'ФИО',
			'email'    => 'Email',
			'role'     => 'Роль (должность)',
			'avatar'   => 'Аватар',
			'registration_date' => 'В компании с',
			'registration_date_caption' => 'В компании с',
			
			'_tasks_active' => 'Активных задач'
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
		$criteria->compare('login',$this->login,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Users the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	
	protected function beforeSave()
	{
		if(parent::beforeSave())
		{
			if ($this->new_password)
				$this->password=$this->hashPassword($this->new_password);

			if ($this->registration_date)
				$this->registration_date = date('Y-m-d 00:00:00', strtotime($this->registration_date));
				
			return true;
		}
		else
			return false;
	}
	
	protected function afterSave()
	{
		// обработка аватары
		if ($this->avatar)
		{
			$path = Yii::getPathOfAlias('webroot.images.avatars') . DIRECTORY_SEPARATOR . $this->id . '.' . self::$avatar_ext;
			
			// сохраняем на диск
			$this->avatar->saveAs($path);
			
			try
			{
				// пересохраняем с нужными размерами и в нужном формате
				$is = getimagesize($path);
				
				$img  = File::createImageFrom($path);

				$w = 50;
				$h = round(($w / $is[0]) * $is[1]);

				if ($h < 50)
				{
					$h = 50;
					$w = round(($h / $is[1]) * $is[0]);
				}
				
				$ri = imagecreatetruecolor($w, $h);
				imagecopyresampled($ri, $img, 0, 0, 0, 0, $w, $h, $is[0], $is[1]);

				$ti = imagecreatetruecolor(50, 50);
				imagecopy($ti, $ri, 0, 0, ($w - 50) / 2, ($h - 50) / 2, $w, $h);

				switch (self::$avatar_ext) {
					case 'gif':
						imagegif($ti, $path, 50);
						break;
				}
			}
			catch (exception  $e) {
				@unlink($path);
			}
		}
		
		return parent::afterSave();
	}
	
	
	public function afterFind()
	{
		// get avatar
		if (file_exists(Yii::getPathOfAlias('webroot.images.avatars') . DIRECTORY_SEPARATOR . $this->id . '.' . self::$avatar_ext))
			$this->avatar = Yii::app()->baseUrl . '/images/avatars/' . $this->id . '.' . self::$avatar_ext;
		else
			$this->avatar = Yii::app()->baseUrl . self::$avatar_def;
			
		if ($this->registration_date == '0000-00-00 00:00:00' || $this->registration_date == '0000-00-00')
		{
			$this->registration_date = '';
			$this->registration_date_caption = 'не известно';
		}
		else
			$this->registration_date_caption = str_replace('00:00:00', '', $this->registration_date);
	}
	
	
	public function validatePassword($password)
    {
        return CPasswordHelper::verifyPassword($password, $this->password);
    }
 
    public function hashPassword($password)
    {
        return CPasswordHelper::hashPassword($password);
    }
	
	/**
	 * @return полный список пользователей
	 */
	public static function getUsersList()
	{
		foreach (self::model()->findAll() AS $u)
		{
			$users[$u->id] = $u;
		}
		
		return (array) $users; 
	}
	
}
