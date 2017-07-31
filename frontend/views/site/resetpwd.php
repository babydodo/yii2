<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = '重置密码';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-resetpwd">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>请填写新密码:</p>

    <div class="row">
        <div class="col-lg-5">
		
		    <?php $form = ActiveForm::begin(); ?>
		 		
		    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>
		    
		    <?= $form->field($model, 'password_repeat')->passwordInput(['maxlength' => true]) ?>
		
		    <div class="form-group">
		        <?= Html::submitButton('重置', ['class' =>'btn btn-success']) ?>
		    </div>
		   
		    <?php ActiveForm::end(); ?>
		
		</div>
    </div>

</div>
