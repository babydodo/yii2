<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Course */

$this->title = '新增课程';
$this->params['breadcrumbs'][] = ['label' => '课程管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="course-create">

    <h2 align="center"><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
