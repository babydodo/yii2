<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Adminuser */

$this->title = '重置密码';
$this->params['breadcrumbs'][] = ['label' => '管理员', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
// 自定义js
$js = <<<JS
$('form#resetpwd-form').on('beforeSubmit', function(e) {
    $('#modal_id').modal('toggle');
    $.ajax({
        url: $(this).attr('action'),
        type: 'post',
        data: $(this).serialize(),
        success: function (data) {
            if(!data===true) {
                alert('保存失败,请刷新重试...');
            }
        }
    });
}).on('submit', function(e){
    e.preventDefault();
});
JS;
$this->registerJs($js);
?>

<div class="adminuser-resetpwd">

    <div class="adminuser-form">

        <?php $form = ActiveForm::begin([
            'id' => 'resetpwd-form',
            'validationUrl' => Url::toRoute(['validate-resetpwd']),
            'layout' => 'horizontal',
        ]); ?>

        <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'password_repeat')->passwordInput(['maxlength' => true]) ?>

        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-6">
                <?= Html::submitButton('确定', ['class' =>'btn btn-primary']) ?>
                <?= Html::a('取消', '#', ['class' =>'btn btn-danger', 'data-dismiss'=>'modal']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
