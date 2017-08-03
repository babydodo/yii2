<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Classes */

$this->title = '修改班级信息: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '班级管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="classes-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
