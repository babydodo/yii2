<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Application */

$this->title = '审核申请';
$this->params['breadcrumbs'][] = ['label' => '审核管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="application-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('通过', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('不通过', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'course_id',
            'user_id',
            'apply_at',
            'apply_week',
            'adjust_week',
            'adjust_day',
            'adjust_sec',
            'classroom_id',
            'teacher_id',
            'type',
            'reason',
            'status',
            'remark',
        ],
    ]) ?>

</div>
