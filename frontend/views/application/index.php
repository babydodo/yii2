<?php

use common\models\Audit;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '申请列表';
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

            'course.name',
            'typeStr',
            ['attribute' => 'statusStr',
            ],
            'apply_at:date',

            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
            ],
        ],
        'rowOptions' => function($model, $key, $index, $grid) {
            $color = [
                Audit::STATUS_FAILED => 'danger',
                Audit::STATUS_UNAUDITED => '',
                Audit::STATUS_PASS => 'success',
            ];
            return ['class' => $color[$model->status]];
        },
        'emptyText'=>'',
        // 'emptyTextOptions'=>['style'=>'color:red;font-weight:bold;font-size:24px'],
        // 'layout' => "{summary}\n{items}\n{pager}"
        'showOnEmpty'=>false,

    ]); ?>
</div>
