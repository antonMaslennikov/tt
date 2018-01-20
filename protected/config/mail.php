<?php
 
return array(
    'viewPath' => 'application.views.mail', //путь к папке с представлениями view
    'layoutPath' => 'application.views.layouts', //путь к папке с макетами layouts
    
    //путь к папке с изображениями (для отправки из консоли)
    'baseDirPath' => 'webroot.images.mail',
 
    'savePath' => 'webroot.assets.mail', //путь к папке для сохранения писем (тестовый режим)
    'testMode' => false, //тестовый режим
    'layout' => 'mail', //основной макет (layout)
    'CharSet' => 'UTF-8', //кодировка
 
    //текст для тех у кого не включено отображение писем содержащих HTML
    'AltBody' => Yii::t('YiiMailer', 'You need an HTML capable viewer to read this message.'),
 
    //языковые настройки ошибок и прочего
    'language' => array(
        'authenticate' => Yii::t('YiiMailer', 'SMTP Error: Could not authenticate.'),
        'connect_host' => Yii::t('YiiMailer', 'SMTP Error: Could not connect to SMTP host.'),
        'data_not_accepted' => Yii::t('YiiMailer', 'SMTP Error: Data not accepted.'),
        'empty_message' => Yii::t('YiiMailer', 'Message body empty'),
        'encoding' => Yii::t('YiiMailer', 'Unknown encoding: '),
        'execute' => Yii::t('YiiMailer', 'Could not execute: '),
        'file_access' => Yii::t('YiiMailer', 'Could not access file: '),
        'file_open' => Yii::t('YiiMailer', 'File Error: Could not open file: '),
        'from_failed' => Yii::t('YiiMailer', 'The following From address failed: '),
        'instantiate' => Yii::t('YiiMailer', 'Could not instantiate mail function.'),
        'invalid_address' => Yii::t('YiiMailer', 'Invalid address'),
        'mailer_not_supported' => Yii::t('YiiMailer', ' mailer is not supported.'),
        'provide_address' => Yii::t('YiiMailer', 'You must provide at least one recipient email address.'),
        'recipients_failed' => Yii::t('YiiMailer', 'SMTP Error: The following recipients failed: '),
        'signing' => Yii::t('YiiMailer', 'Signing Error: '),
        'smtp_connect_failed' => Yii::t('YiiMailer', 'SMTP Connect() failed.'),
        'smtp_error' => Yii::t('YiiMailer', 'SMTP server error: '),
        'variable_set' => Yii::t('YiiMailer', 'Cannot set or reset variable: ')
    ),
	
	'Mailer' => 'smtp',
	'Host' => 'smtp.majordomo.ru',
	'Port' => 25,
	'SMTPSecure' => 'tls',
	'SMTPAuth' => true,
	'Username' => 'tt@tomdom.ru',
	'Password' => '15qGDh7n',
);