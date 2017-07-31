<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ApplicationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '停调课申请';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="application-index">

    <p>
        <?= Html::a('填写申请', ['apply'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'course_id',
            'user_id',
            'apply_at',
            'apply_week',
            // 'adjust_week',
            // 'adjust_day',
            // 'adjust_sec',
            // 'classroom_id',
            // 'teacher_id',
            // 'type',
            // 'reason',
            'status',
            // 'remark',

            ['class' => 'yii\grid\ActionColumn'],
        ],
        'emptyText'=>'',
        // 'emptyTextOptions'=>['style'=>'color:red;font-weight:bold;font-size:24px'],
        // 'layout' => "{summary}\n{items}\n{pager}"
        'showOnEmpty'=>false,
    ]); ?>
</div>
