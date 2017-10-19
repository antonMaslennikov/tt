<?php

/**
 * This is the model class for table "tt_files".
 *
 * The followings are the available columns in table 'tt_files':
 * @property integer $id
 * @property string $path
 */
class File extends CActiveRecord
{
	/**
	 * округлённое значение размера файла
	 */ 
	var $size_rounded;
	/**
	 * оригинальное название файла до загрузки
	 */ 
	var $real_filename;
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tt_files';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('path', 'required'),
			array('path', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, path, mime, extention, size, user_id', 'safe', 'on'=>'search'),
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
		);
	}
	
	protected function beforeSave()
	{
		if (parent::beforeSave())
		{
			if (!$this->user_id)
				$this->user_id=Yii::app()->user->id;
		
			return true;
		}
		else
			return false;
	}
	
	public function afterFind()
	{
		$this->size_rounded = round($this->size / 1024 / 1024, 1);
		
		if ($this->size_rounded < 1)
			$this->size_rounded = round($this->size / 1024) . ' Kb';
		else
			$this->size_rounded .= ' Mb';
		
		$this->real_filename = substr(basename($this->path), 11);
	}
	
	/**
	 * до удаления файла проверяем его авторство
	 * удалять может только тот кто его загрузил
	 */
	public function beforeDelete() {
		if(parent::beforeDelete())
		{
			if ($this->user_id != Yii::app()->user->id)	{
				header('HTTP/1.0 500 Error');
				return false;
			}
			
			return true;
		}
		else
			return false;
	}
	
	/**
	 * до удаления модели удаляем файл на диске
	 */
	public function afterDelete() {
		
		@unlink(Yii::getpathOfAlias('webroot') . $this->path);

		return parent::afterDelete();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'path' => 'путь',
			'mime' => 'mime-тип',
			'extention' => 'расширение файла',
			'size' => 'размер файла',
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
		$criteria->compare('path',$this->path,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return File the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	
	/**
	 * @param string путь до файла
	 * return экземпляр класс gd2 
	 */ 
	public static function createImageFrom($path)
	{
		$imgData = getimagesize($path);

		switch ($imgData[2]) {
			case 1:	// GIF
				$i = @imagecreatefromgif($path);
				break;
			case 2:	// JPG
				$i = @imagecreatefromjpeg($path);
				break;
			case 3:	// PNG
				$i = @imagecreatefrompng($path);
				break;
			default:
				return false;
				break;
		}
		
		return $i;
	}
	
	function createDir($path)
	{
		if (!is_dir(Yii::getPathOfAlias('webroot') . $path))
		{
			$path = explode(DIRECTORY_SEPARATOR, dirname($path));

			umask(0002);
			$ppath = Yii::getPathOfAlias('webroot');

			foreach($path as $f)
			{
				if (!empty($f))
				{
					$ppath .= DIRECTORY_SEPARATOR . $f;
					
					if (!is_dir($ppath)) mkdir($ppath, 0775);
				}
			}
		}
	}
}
