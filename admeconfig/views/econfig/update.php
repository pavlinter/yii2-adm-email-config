<?php

use yii\helpers\Html;
use pavlinter\adm\Adm;

/* @var $this yii\web\View */
/* @var $model \pavlinter\admeconfig\models\EmailConfig */
/* @var $paramsValue array|null */
/* @var $data array */

Yii::$app->i18n->disableDot();
$this->title = Adm::t('adm_email_config', 'Mail Config');
$this->params['breadcrumbs'][] = $this->title;
Yii::$app->i18n->resetDot();
?>
<div class="email-config-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'paramsValue' => $paramsValue,
        'data' => $data,
    ]) ?>
</div>
