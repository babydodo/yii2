<?php

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
            ['class' => 'yii\grid\SerialColumn'],

            'number',
            'name',
            ['attribute' => 'teacher',
             'label' => '教师',
             'value' => 'user.nickname',
            ],
            ['attribute' => 'day',
             'label' => '星期',
             'value' => 'dayStr',
             'filter' => \common\models\Course::allDays(),
            ],
            'sec',
            'week',
            ['attribute' => 'classroomName',
             'label' => '教室',
             'value' => 'classroom.name',
            ],

            ['class' => 'yii\grid\ActionColumn',
             'template'=>'{update} {delete}'],
        ],
    ]); ?>
</div>
