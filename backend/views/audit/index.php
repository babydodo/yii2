<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '审核管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="application-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            ['label' => '申请人',
             'value' => 'application.user.nickname',
            ],
            'application.apply_at:date',
            ['label' => '调整类型',
             'value' => 'application.typeStr',
            ],
            ['label' => '审核',
             'value' => 'statusStr',
            ],

            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function($url,$model,$key) {
                        $options = [
                            'title'=>'查看与审核',
                            'aria-label'=>'查看与审核',
                        ];
                        return Html::a('<span class="glyphicon glyphicon-edit"></span>', $url, $options);
                    },

                ],
            ],
        ],
        'emptyText'=>'当前没有申请, 无需审核~',
        'emptyTextOptions'=>['style'=>'color:red;font-weight:bold;font-size:24px'],
        // 'layout' => "{summary}\n{items}\n{pager}"
        'showOnEmpty'=>false,
    ]); ?>
</div>
