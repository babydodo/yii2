<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Course */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="course-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'number')->textInput() ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'user_id')->dropDownList(\common\models\User::allTeachers()) ?>

    <?= $form->field($model, 'day')->dropDownList(\common\models\Course::allDays()) ?>

    <?= $form->field($model, 'sec')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'week')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'classroom_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新增' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
