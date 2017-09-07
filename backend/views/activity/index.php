<?php

use common\models\Course;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ActivitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '课外活动管理';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="activity-index">

    <p>
        <?= Html::a('新增课外活动', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            // 序号列
            ['class' => 'yii\grid\SerialColumn'],

            // 内容列
            'name',
            'week',
            ['attribute' => 'day',
             'value' => 'dayStr',
             'filter' => Course::allDays(),
            ],
            'sec',
            ['attribute' => 'classroomName',
             'label' => '地点',
             'value' => 'classroom.name',
            ],

            // 动作列
            ['class' => 'yii\grid\ActionColumn',
             'template'=>'{update} {delete}',
            ],
        ],
    ]); ?>
</div>
