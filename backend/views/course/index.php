<?php

use common\models\Course;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\Course */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '课程管理';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="course-index">

    <p>
        <?= Html::a('新增课程', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            // 序号列
            ['class' => 'yii\grid\SerialColumn'],

            // 内容列
            'number',
            'name',
            ['attribute' => 'teacher',
             'value' => 'user.nickname',
            ],
            'week',
            ['attribute' => 'day',
             'value' => 'dayStr',
             'filter' => Course::allDays(),
            ],
            'sec',
            ['attribute' => 'classroomName',
             'value' => 'classroom.name',
            ],

            // 动作列
            ['class' => 'yii\grid\ActionColumn',
             'template'=>'{update} {delete}',
            ],
        ],
    ]); ?>
</div>
