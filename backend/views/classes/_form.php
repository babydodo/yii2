<?php

use common\models\Adminuser;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Classes */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="classes-form">

    <?php
    $validationUrl = ['validate-save'];
    if (!$model->isNewRecord) {
        $validationUrl['id'] = $model->id;
    }

    $form = ActiveForm::begin([
        'id' => 'save-form',
        'validationUrl' => Url::toRoute($validationUrl),
        'layout' => 'horizontal',
    ]); ?>

    <?= $form->field($model, 'number', ['enableAjaxValidation' => true])->textInput() ?>

    <?= $form->field($model, 'name', ['enableAjaxValidation' => true])->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'adminuser_id')->dropDownList(Adminuser::allCounselors(),['prompt'=>'请选择辅导员']);?>

    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-6">
        <?= Html::submitButton($model->isNewRecord ? '新增' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::a('取消', '#', ['class' =>'btn btn-danger', 'data-dismiss'=>'modal']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
