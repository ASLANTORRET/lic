<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'LoveIsCarrot',
    'sourceLanguage'=>'ru',
    'aliases' => array(
        'bootstrap'=> realpath(__DIR__ . '/../extensions/bootstrap'), // change this if necessary
        'yiistrap'=> realpath(__DIR__ . '/../extensions/yiistrap')
    ),
	// preloading 'log' component
	'preload'=>array('log', 'booster'),


	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
        'bootstrap.helpers.*',
        'yiistrap.helpers.*'
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
            'generatorPaths' => array('bootstrap.gii'),
			'password'=>'LiOn8411',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			//'ipFilters'=>array('127.0.0.1','::1'),
            'ipFilters'=>array(),
            'ipFilters'=>false,
            'generatorPaths' => array('yiistrap.gii'),
		),
	),

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),

        /*'cache'=>array(
            'class'=>'system.caching.CMemCache',
            'servers'=>array(
                array('host'=>'127.0.0.1', 'port'=>11211)
            ),
        ),*/

        'booster' => array(
            'class' => 'bootstrap.components.Booster',
        ),

        'bootstrap' => array(
            'class' => 'yiistrap.components.TbApi',
        ),

		// uncomment the following to enable URLs in path-format

		'urlManager'=>array(
			'urlFormat'=>'path',
            'showScriptName'=>false,
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),


		// 'db'=>array(
		// 	'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
		// ),
		// uncomment the following to use a MySQL database
		
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=db_lic',
			'emulatePrepare' => true,
			'username' => 'ussdkz',
			'password' => '290X7b80q7',
			'charset' => 'utf8',
            'enableParamLogging'=>false
		),
		
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		/*'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
			),
		),*/
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'aslantorret@gmail.com',
        'timeout'=>'86400',
        'form'=>array(
            'sex'=>array(
                '0'=>'не указал',
                '1'=>'мужской',
                '2'=>'женский'
            ),
            'agelic'=>array(
                '0'=>'не указал',
                '1'=>'15-19',
                '2'=>'20-28',
                '3'=>'29+'
            ),
            'agediary'=>array(
                '0'=>'не указал',
                '1'=>'15-25',
                '2'=>'26-36',
                '3'=>'37+'
            ),
            'relation'=>array(
                '0'=>'не указал',
                '1'=>'замужем/женат',
                '2'=>'в начале отношений',
                '3'=>'в поиске'
            ),
            'physics'=>array(
                '0'=>'не указал',
                '1'=>'продвинутый',
                '2'=>'новичок',
                '3'=>'никогда не занимался'
            )
        ),
        'interface'=>array(
            'is_visible'=>array(
                '1'=>'да',
                '0'=>'нет'
                ),
            'sex'=>array(
                ''=>'--все--',
                '0'=>'не указал',
                '1'=>'мужской',
                '2'=>'женский'
                ),

            'age'=>array(

                '2' => array(
                    ''=>'--все--',
                    '0'=>'не указал',
                    '1'=>'15-19',
                    '2'=>'20-28',
                    '3'=>'29+'
                ),

                '3' => array(
                    ''=>'--все--',
                    '0'=>'не указал',
                    '1'=>'15-25',
                    '2'=>'26-36',
                    '3'=>'37+'
                ),

            ),

            'agelic'=>array(
                 ''=>'--все--',
                 '0'=>'не указал',
                 '1'=>'15-19',
                 '2'=>'20-28',
                 '3'=>'29+'
                ),

            'agediary'=>array(
                 ''=>'--все--',
                 '0'=>'не указал',
                 '1'=>'15-25',
                 '2'=>'26-36',
                 '3'=>'37+'
                ),

            'relation'=>array(
                ''=>'--все--',
                '0'=>'не указал',
                '1'=>'замужем/женат',
                '2'=>'в начале отношений',
                '3'=>'в поиске'
            ),

            'physics'=>array(
                ''=>'--все--',
                '0'=>'не указал',
                '1'=>'Продвинутый',
                '2'=>'Новичок',
                '3'=>'Мечтатель'
            ),

            'categories'=>array(
                ''=>'--все--',
                '2'=>'Любовь-Морковь',
                '3'=>'Дневник здоровья'
            ),

            'charging_smscost'=>array(
                ''=>'--все--',
                '10'=>'0',
                '20'=>'0',
                '11'=>'30'
            )
        ),
        'messages'=>array(

            'common' => array(
                'SMS_default' => "Сервис временно недоступен. Попробуйте позднее",
                'SMS_AOC' => 'Чтобы подписаться на услугу отправьте ДА в ответ на это сообщение',
                'SMS_incorrect' => "Некорректное сообщение.",
                'USSD_default' => "Servis vremenno nedostupen. Poprobuite pozdnee",
                'SubscribedAlready' => 'Izvinite, Vy uzhe podpisany',
                'SubscribedAlreadySMS' => 'Вы уже подписаны на эту услугу',
                'NotSubscribed' => 'Vy ne podpisany na etu uslugu.',
                'Subscribe' => "Vy podpisalis' na uslugu 'category_name'. Vam otpravleno SMS.",
            ),

            '2' => array(                   //Любовь - Морковь

                'SMS_Subscribe' => "Вы подписались на услугу «Любовь-Морковь». Чтобы получать уникальные сведения от журналов «Cosmo» и «Mens Health»,  наберите *603*8%23 и введите анкетные данные. Стоимость – 30 тг/2 дня. Для отмены подписки наберите команду *603*10%23 или отправьте СТОП на короткий номер 3012.",

                'USSD_Subscribe' => "Vy podpisalis na uslugu Lyubov-Morkov. Chtoby izmenit svoi anketnye dannye naberite *603*8#",

                //Unsubscribe

                'UnsubscribeByUSSD' => "Vy otpisalis ot uslugi Lyubov-Morkov. Podpisatsya zanovo - *603*7#",
                'UnsubscribeBySMS' => "Вы отписались от услуги «Любовь-Морковь». Для повторной подписки отправьте слово LOVE на 3012 или наберите команду *603*7#",       //Не законченный

                //USSD text

                'Sex' => 'Podpiska oformlena. Pozhaluysta, ukazhite pol:',
                'Age' => "Dannye prinyaty. Teper, ukazhite vozrast:",
                'Relation' => 'Dannye prinyaty. Ukazhite status otnosheniy:',

                //Informative messages

                'Info' => "Usluga Lyubov-Morkov ot zhurnalov Cosmo i Men's Health. Stoimost' - 30tg/2 dnya.",
                'FilledProfile' => "Pozdravlyaem, vy udachno zapolnili anketu. Chtoby izmenit' svoi anketnye dannye, naberite *603*8#",
                'ProfileAlreadyFilled' => 'Услуга «Любовь-Морковь». Вы уже заполнили анкету. Чтобы редактировать свою анкету, наберите *603*8#',
                'NotificationSubsInfo' => 'На данном номере активна подписка на сервис Любовь-Морковь (30тг/2 дн). Чтобы изменить анкету, наберите *603*8# Для отписки от услуги отправь СТОП на номер 3012. Оставайтесь на волне вместе с Cosmo и Mens Health! Поддержка help@zerogravity.kz',
                //Call to action message ERROR

                'SubscribeOffer' => "Ошибка! Вы не подписаны на сервис «Любовь-Морковь» от журналов «Cosmo» и «Men's Health». Для подписки отправьте слово LOVE на 3012 или наберите команду *603*1*1%23",
                'FillSex'=>"Услуга «Любовь-Морковь». Некорректное сообщение",
                'FillAge'=>"Услуга «Любовь-Морковь». Некорректное сообщение",
                'FillRelation'=>"Услуга «Любовь-Морковь». Некорректное сообщение",

                //Missed message text

                'MissedSex'=>"Сервис «Любовь-Морковь» просит Вас указать свой ПОЛ, чтобы получать индивидуальную информацию от журналов «Cosmo» & «Men's Health»\n*Ответы принимаются только цифрами от 1 до 2.\n1.Мужской\n2.Женский ",
                'MissedAge'=>"Сервис «Любовь-Морковь» просит Вас указать свой ВОЗРАСТ, чтобы получать индивидуальную информацию от журналов «Cosmo» & «Men's Health»\n*Ответы принимаются только цифрами от 1 до 3.\n1.15-19 лет.\n2.20-28 лет.\n3.29 и выше.",
                'MissedRelation'=>"Сервис «Любовь-Морковь» просит Вас указать свой СТАТУС отношений, чтобы получать индивидуальную информацию от журналов «Cosmo» & «Men's Health»\n*Ответы принимаются только цифрами от 1 до 3.\n1.Женат/Замужем.\n2.В начале отношений.\n3.В поиске.",

                //Confirmation message text

                'SexConfirmed'=>"Спасибо! Пол подтвержден! Чтобы получать уникальную информацию от журналов «Cosmo» & «Men's Health», укажите свой ВОЗРАСТ. *Ответы принимаются только цифрами от 1 до 3.\n1.15-19 лет.\n2.20-28 лет.\n3.29 и выше.",
                'AgeConfirmed'=>"Спасибо! Возраст подтвержден! Чтобы получать уникальную информацию от журналов «Cosmo» & «Men's Health», укажите СТАТУС ваших отношений. *Ответы принимаются только цифрами от 1 до 3. \n1.Женат/Замужем.\n2.В начале отношений.\n3.В поиске.",
                'RelationConfirmedMan'=>"Спасибо! Ваши анкетные данные приняты. Теперь вы будете получать только самую интересную и уникальную информацию от журнала «Men's Health».",
                'RelationConfirmedWoman'=>"Спасибо! Ваши анкетные данные приняты. Теперь вы будете получать только самую интересную и уникальную информацию от журнала «Cosmo».",
                'QuestionConfirmed'=>"Спасибо! Ваши ответ принят.",

                //ERRORS
                //'Letters' => "Услуга «Любовь-Морковь». Принимаются только цифры от 1-3 (включительно)",
                'Letters' => "Услуга «Любовь-Морковь». Некорректное сообщение",

            ),

            '3' => array(                       //Дневник здоровья

                //Unsubscribe

                'UnsubscribeByUSSD' => "Vy otpisalis ot uslugi Dnevnik zdoroviya. Podpisatsya zanovo - *603*40#",
                'UnsubscribeBySMS' => "Вы отписались от услуги «Дневник здоровья». Подписаться заново - *603*40#",       //Не законченный

                //Subscribe

                'SMS_Subscribe' => "Вы подписались на услугу «Дневник Здоровья». Чтобы получать уникальные сведения от журналов «Cosmo» и «Men's Health»,  наберите *603*41%23 и введите анкетные данные. Стоимость – 30 тг/2 дня. Для отмены подписки наберите команду *603*42%23 или отправьте СТОП на короткий номер 3013.",
                'USSD_Subscribe' => "Vy podpisalis' na uslugu Dnevnik zdoroviya. Chtoby izmenit svoi anketnye dannye naberite *603*41#",

                //USSD text

                'Sex' => "Podpiska oformlena. Pozhaluysta, ukazhite pol:",
                'Age' => "Dannye prinyaty. Teper, ukazhite vozrast:",
                'Physics' => "Dannye prinyaty. Ukazhite uroven podgotovki:",

                //Informative messages

                'Info' => "Usluga Dnevnik zdoroviya. Stoimost - 30tg/2 dnya.",
                'FilledProfile' => "Pozdravlyaem, vy udachno zapolnili anketu. Chtoby izmenit svoi anketnye dannye, naberite *603*41#",
                'ProfileAlreadyFilled' => 'Услуга «Дневник здоровья». Вы уже заполнили анкету. Чтобы редактировать свою анкету, наберите *603*41#',
                'NotificationSubsInfo' => 'На данном номере активна подписка на сервис Дневник Здоровья (30тг/2 дн). Чтобы изменить анкету, наберите *603*41# Для отписки от услуги отправь СТОП на номер 3013. Оставайтесь на волне вместе с Cosmo и Mens Health! Поддержка help@zerogravity.kz',

                //Call to action message ERROR

                /*'SubscribeOffer' => "Ошибка! Вы не подписаны на сервис «Дневник здоровья» от журналов «Cosmo» и «Men's Health». Для подписки отправьте слово FIT на 3013 или наберите команду *603*40#",
                'FillSex'=>"Некорректное сообщение! Чтобы получать индивидуальную информацию от журналов «Cosmo» и «Men's Health», укажите свой ПОЛ. *Ответы принимаются только цифрами от 1 до 2.\n1.Мужской\n2.Женский",
                'FillAge'=>"Некорректное сообщение! Чтобы получать индивидуальную информацию от журналов «Cosmo» и «Men's Health», укажите свой ВОЗРАСТ. *Ответы принимаются только цифрами от 1 до 3.\n1.15-25 лет.\n2.26-36 лет.\n3.37 и выше.",
                'FillPhysics'=>"Некорректное сообщение! Чтобы получать индивидуальную информацию от журналов «Cosmo» и «Men's Health», укажите УРОВЕНЬ вашей физической подготовки. *Ответы принимаются только цифрами от 1 до 3.\n1.Продвинутый.\n2.Новичок.\n3.Мечтатель .",*/

                'SubscribeOffer' => "Ошибка! Вы не подписаны на сервис «Дневник здоровья» от журналов «Cosmo» и «Men's Health». Для подписки отправьте слово FIT на 3013 или наберите команду *603*40#",
                'FillSex'=>"Услуга «Дневник здоровья». Некорректное сообщение",
                'FillAge'=>"Услуга «Дневник здоровья». Некорректное сообщение",
                'FillPhysics'=>"Услуга «Дневник здоровья». Некорректное сообщение",

                //Missed message text

                'MissedSex'=>"Сервис «Дневник здоровья» просит Вас указать свой ПОЛ, чтобы получать индивидуальную информацию от журналов «Cosmo» & «Men's Health»\n*Ответы принимаются только цифрами от 1 до 2.\n1.Мужской\n2.Женский ",
                'MissedAge'=>"Сервис «Дневник здоровья» просит Вас указать свой ВОЗРАСТ, чтобы получать индивидуальную информацию от журналов «Cosmo» & «Men's Health»\n*Ответы принимаются только цифрами от 1 до 3.\n1.15-25 лет.\n2.26-36 лет.\n3.37 и выше.",
                'MissedPhysics'=>"Сервис «Дневник здоровья» просит Вас указать свой УРОВЕНЬ физической подготовки, чтобы получать индивидуальную информацию от журналов «Cosmo» & «Men's Health»\n*Ответы принимаются только цифрами от 1 до 3.\n1.Продвинутый.\n2.Новичок.\n3.Мечтатель .",

                //Confirmation message text

                'SexConfirmed'=>"Спасибо! Пол подтвержден! Чтобы получать уникальную информацию от журналов «Cosmo», укажите свой возраст. *Ответы принимаются только цифрами от 1 до 3.\n1.15-25 лет.\n2.26-36 лет.\n3.37 и выше.",
                'AgeConfirmed'=>"Спасибо! Возраст подтвержден! Чтобы получать уникальную информацию от журналов«Cosmo», укажите уровень своей физической подготовки. *Ответы принимаются только цифрами от 1 до 3.\n1.Продвинутый.\n2.Новичок.\n3.Мечтатель.",
                'PhysicsConfirmedMan'=>"Спасибо! Ваши анкетные данные приняты. Теперь вы будете получать только самую интересную и уникальную информацию от журнала «Men's Health».",
                'PhysicsConfirmedWoman'=>"Спасибо! Ваши анкетные данные приняты. Теперь вы будете получать только самую интересную и уникальную информацию от журнала «Cosmo».",
                'QuestionConfirmed'=>"Спасибо! Ваши ответ принят.",

                //ERRORS
                'Letters' => "Услуга «Дневник здоровья». Принимаются только цифры от 1-3 (включительно)"
            )

        ),
        'system'=>array(
            'sex'=>array(
                '1'=>'Muzhskoy',
                '2'=>'Zhenskiy'
            ),
            'age'=>
                array(

                    '2' => array(
                        '1'=>'15-19',
                        '2'=>'20-28',
                        '3'=>'29+'
                    ),

                    '3' => array(
                        '1'=>'15-25',
                        '2'=>'26-36',
                        '3'=>'37+'
                    )

            ),
            'relation'=>array(
                '1'=>array(
                    '1'=>'zhenat',
                    '2'=>'v nachale otnosheniy',
                    '3'=>'v poiske'
                ),
                '2'=>array(
                    '1'=>'zamuzhem',
                    '2'=>'v nachale otnosheniy',
                    '3'=>'v poiske'
                )

            ),

            'physics'=>array(
                '1'=>array(
                    '1'=>'prodvinutiy',
                    '2'=>'novichok',
                    '3'=>'nikogda ne zanimalsa'
                ),
                '2'=>array(
                    '1'=>'prodvinutiy',
                    '2'=>'novichok',
                    '3'=>'nikogda ne zanimalsa'
                )

            )

        ),
        'catID_paramID'=>array(
            '11'=>'1',
            '12'=>'2',
            '13'=>'1',
            '14'=>'1',
            '15'=>'2',
            '16'=>'2',
            '17'=>'3',
            '18'=>'3',
            '20'=>'1',
            '21'=>'1',
            '22'=>'1',
            '23'=>'2',
            '24'=>'2',
            '25'=>'2',
            '26'=>'3',
            '27'=>'3',
            '28'=>'3',
            '29'=>'1',
            '30'=>'1',
            '31'=>'1',
            '32'=>'2',
            '33'=>'2',
            '34'=>'2',
            '35'=>'3',
            '36'=>'3',
            '37'=>'3',
            '43'=>'1',
            '45'=>'1',
            '48'=>'1',
            '51'=>'1',
            '54'=>'1',
            '57'=>'1',
            '60'=>'1',
            '63'=>'1',
            '65'=>'1',
            '44'=>'2',
            '46'=>'2',
            '49'=>'2',
            '52'=>'2',
            '55'=>'2',
            '58'=>'2',
            '61'=>'2',
            '64'=>'2',
            '66'=>'2',
            '47'=>'3',
            '50'=>'3',
            '53'=>'3',
            '56'=>'3',
            '59'=>'3',
            '62'=>'3',
            '67'=>'3',
            '68'=>'3',
                                      //Закончить
        ),
        'charging_smscost'=>array(
            '10'=>'5',
            '11'=>'30'
        ),

        'sh_charging_cost'=>array(      //для статистики
            '3012_10' => '0',
            '3012_11' => '30',
            '3012_20' => '0',
            '3013_10' => '0',
            '3013_11' => '30',
            '3013_20' => '0'
        )
	),
);