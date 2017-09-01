<?php

use common\models\Audit;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '审核管理';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
$viewUrl = Url::toRoute('view');
$js = <<<JS
    $('.show-audit').on('click',function () {
        $('#modal_id').find('.modal-title').html('审核');
        $.get('{$viewUrl}', { id:$(this).closest('tr').data('key') },
            function (data) {
                $('#modal_id').find('.modal-body').html(data);
            }
        );
    });
JS;
$this->registerJs($js);
?>

<div class="application-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            ['label' => '申请人',
             'value' => 'application.user.nickname',
            ],
            ['label' => '调整类型',
             'value' => 'application.typeStr',
            ],
            'application.apply_at:date',
            ['label' => '状态',
             'value' => 'statusStr',
             'contentOptions' => function($model) {
                return ($model->status== Audit::STATUS_UNAUDITED)?['class'=>'text-danger']:[];
            }],

            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function($url,$model,$key) {
                        $options = [
                            'title'=>'详情',
                            'aria-label'=>'详情',
                            'class' => 'show-audit',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_id',
                        ];
                        return Html::a('<span class="glyphicon glyphicon-edit"></span>','#',$options);
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
