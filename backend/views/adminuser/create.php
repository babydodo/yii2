<?php

use common\models\Adminuser;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model common\models\Adminuser */

$this->title = '新增管理员';
$this->params['breadcrumbs'][] = ['label' => '管理员', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="adminuser-create">

    <div class="adminuser-form">

        <?php $form = ActiveForm::begin([
            'id' => 'create-form',
            'validationUrl' => Url::toRoute(['validate-save']),
            'layout' => 'horizontal',
        ]); ?>

        <?= $form->field($model, 'username', ['enableAjaxValidation' => true])->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'nickname')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'role')->dropDownList(Adminuser::allRoles(), ['prompt'=>'请选择角色']) ?>

        <?= $form->field($model, 'password')->passwordInput() ?>

        <?= $form->field($model, 'password_repeat')->passwordInput() ?>

        <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-6">
                <?= Html::submitButton('新增', ['class' =>'btn btn-success']) ?>
                <?= Html::a('取消', '#', ['class' =>'btn btn-danger', 'data-dismiss'=>'modal']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
