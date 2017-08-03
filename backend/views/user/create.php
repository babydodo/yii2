<?php

use common\models\Classes;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = '新增用户';
$this->params['breadcrumbs'][] = ['label' => '用户管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <div class="user-form">

        <?php $form = ActiveForm::begin([
            'id' => 'create-form',
            'validationUrl' => Url::toRoute(['validate-save']),
            'layout' => 'horizontal',
        ]); ?>

        <?= $form->field($model, 'username', ['enableAjaxValidation' => true])->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'nickname')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'password')->passwordInput() ?>

        <?= $form->field($model, 'password_repeat')->passwordInput() ?>

        <?= $form->field($model,'class_id')->dropDownList(Classes::allClasses(),['prompt'=>'请选择']);?>

        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-6">
                <?= Html::submitButton('新增', ['class' =>'btn btn-success']) ?>
                <?= Html::a('取消', '#', ['class' =>'btn btn-danger', 'data-dismiss'=>'modal']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
