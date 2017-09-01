<?php

use common\models\Course;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '课程管理';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="course-index">

    <p>
        <?= Html::a('新增课外活动', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            'week',
            ['attribute' => 'day',
             'label' => '星期',
             'value' => 'dayStr',
            ],
            'sec',
            ['attribute' => 'classroomName',
             'label' => '地点',
             'value' => 'classroom.name',
            ],

            ['class' => 'yii\grid\ActionColumn',
             'template'=>'{update} {delete}',
            ],
        ],
        'emptyText'=>'',
        'showOnEmpty'=>false,
    ]); ?>
</div>
