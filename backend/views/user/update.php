<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = '修改用户信息: ' . $model->nickname;
$this->params['breadcrumbs'][] = ['label' => '用户管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="user-update">

    <div class="user-form">

        <?php $form = ActiveForm::begin([
            'id' => 'update-form',
            'validationUrl' => Url::toRoute(['validate-save', 'id'=>$model->id]),
            'layout' => 'horizontal',
        ]); ?>

        <?= $form->field($model, 'username', ['enableAjaxValidation' => true])->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'nickname')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model,'class_id')->dropDownList(\common\models\Classes::allClasses());?>

        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-6">
                <?= Html::submitButton('修改', ['class' =>'btn btn-primary']) ?>
                <?= Html::a('取消', '#', ['class' =>'btn btn-danger', 'data-dismiss'=>'modal']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
