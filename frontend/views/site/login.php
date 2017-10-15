<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\LoginForm */

$this->title = '登陆';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <div  style="padding-top: 10%" class="col-sm-offset-3 col-lg-6">
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title text-center">停调课管理系统</h3></div>
            <div class="panel-body">
            <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

                <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

                <?= $form->field($model, 'password')->passwordInput() ?>

                <?= $form->field($model, 'rememberMe')->checkbox() ?>

                <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-6">
                        <?= Html::submitButton('登陆', ['class' => 'btn btn-default', 'name' => 'login-button']) ?>
                    </div>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
    </div>
</div>
