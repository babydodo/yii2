<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \backend\models\ExcelUpload  */

$this->title = '停调课管理系统';
?>
<div class="site-index">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <?= $form->field($model, 'file')->fileInput() ?>

    <?= Html::submitButton('提交') ?>

    <?php ActiveForm::end() ?>

</div>
