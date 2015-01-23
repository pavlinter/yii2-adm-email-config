Yii2: Adm-Email-Config Модуль для Adm CMS
================

Установка
------------
Удобнее всего установить это расширение через [composer](http://getcomposer.org/download/).

```
   "pavlinter/yii2-adm-email-config": "*",
```

Настройка
-------------
```php
'on beforeRequest' => function ($event) {

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
```

Запустить миграцию
-------------
```php
yii migrate --migrationPath=@vendor/pavlinter/yii2-adm-email-config/admeconfig/migrations
```