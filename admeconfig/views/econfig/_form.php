<?php

use kartik\checkbox\CheckboxX;
use pavlinter\buttons\InputButton;
use pavlinter\adm\Adm;

/* @var $this yii\web\View */
/* @var $model\pavlinter\admeconfig\models\EmailConfig */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="email-config-form">

    <?php $form = Adm::begin('ActiveForm'); ?>

    <div class="row">
        <div class="col-xs-12 col-sm-4 col-md-4 readonly-cont">
            <?= $form->field($model, 'host')->textInput(['maxlength' => 250]) ?>

            <?= $form->field($model, 'port')->textInput(['maxlength' => 50]) ?>

            <?= $form->field($model, 'encryption')->widget(\kartik\widgets\Select2::classname(), [
                'data' => $model::encryptionList(),
            ]); ?>
        </div>

        <div class="col-xs-12 col-sm-4 col-md-4">
            <?= $form->field($model, 'from_email')->textInput(['maxlength' => 250]) ?>

            <?= $form->field($model, 'from_name')->textInput(['maxlength' => 250]) ?>
        </div>

        <div class="col-xs-12 col-sm-4 col-md-4 readonly-cont">
            <?= $form->field($model, 'username')->textInput(['maxlength' => 250]) ?>

            <?= $form->field($model, 'password')->textInput(['maxlength' => 250]) ?>

            <?= $form->field($model, 'enable_smtp', ["template" => "{input}\n{label}\n{hint}\n{error}"])->widget(CheckboxX::classname(), ['pluginOptions'=>['threeState' => false]]); ?>

        </div>
    </div>

    <div class="form-group">
        <?=  InputButton::widget([
            'label' => $model->isNewRecord ? Adm::t('', 'Create', ['dot' => false]) : Adm::t('', 'Update', ['dot' => false]),
            'options' => ['class' => 'btn btn-primary'],
            'input' => 'adm-redirect',
            'name' => 'redirect',
            'formSelector' => $form,
        ]);?>
    </div>

    <?php Adm::end('ActiveForm'); ?>

</div>

<?php
$this->registerJs('

    $("#emailconfig-enable_smtp").on("change", function(){
        $(this).trigger("readonly");
    });

    $("#emailconfig-enable_smtp").on("readonly", function(){
        $inp = $(".readonly-cont input").not(this);
        console.log($inp);
        if($(this).val() == 1){
            $inp.prop("readonly", false);
            $("#emailconfig-encryption").select2("readonly", false);
        } else {
            $inp.prop("readonly", true);
            $("#emailconfig-encryption").select2("readonly", true);
        }

    }).trigger("readonly");
');