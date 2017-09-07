<?php

/* @var $this yii\web\View */
/* @var $model common\models\Classroom */

$this->title = '修改教室信息: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '教室管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="classroom-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
