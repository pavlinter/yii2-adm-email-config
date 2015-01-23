Yii2: Adm-Email-Config Модуль для Adm CMS
===================

Установка
-------------------
Удобнее всего установить это расширение через [composer](http://getcomposer.org/download/).

```
   "pavlinter/yii2-adm-email-config": "*",
```

Настройка
-------------------
```php
'on beforeRequest' => function ($event) {
    \pavlinter\admeconfig\models\EmailConfig::changeMailConfig();
},
'modules' => [
    ...
    'adm' => [
        ...
        'modules' => [
            'admeconfig'
        ],
        ...
    ],
    'admeconfig' => [
        'class' => 'pavlinter\admeconfig\Module',
    ],
    ...
],
'components' => [
    ...
    'mailer' => [
        'class' => 'yii\swiftmailer\Mailer',
    ],
    ...
],
```

Запустить миграцию
-------------------
```php
yii migrate --migrationPath=@vendor/pavlinter/yii2-adm-email-config/admeconfig/migrations
```

Как использовать
-------------------
```php
Yii::$app->mailer->compose()
    ->setTo('test@test.com')
    ->setFrom(Yii::$app->params['adminEmailName'])
    //->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->params['adminName']])
    ->setSubject('subject')
    ->setTextBody('body')
    ->send();
```