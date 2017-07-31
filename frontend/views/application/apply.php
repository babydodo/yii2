<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Application */
/* @var $form yii\widgets\ActiveForm */

$this->title = '停调课申请';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-apply">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="application-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'course_id')->textInput() ?>

        <?= $form->field($model, 'user_id')->textInput() ?>

        <?= $form->field($model, 'apply_at')->textInput() ?>

        <?= $form->field($model, 'apply_week')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'adjust_week')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'adjust_day')->textInput() ?>

        <?= $form->field($model, 'adjust_sec')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'classroom_id')->textInput() ?>

        <?= $form->field($model, 'teacher_id')->textInput() ?>

        <?= $form->field($model, 'type')->textInput() ?>

        <?= $form->field($model, 'reason')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'status')->textInput() ?>

        <?= $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('提交', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
