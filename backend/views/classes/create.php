<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Classes */

$this->title = '新增班级';
$this->params['breadcrumbs'][] = ['label' => '班级管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="classes-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
