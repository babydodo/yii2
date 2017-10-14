<?php

use common\models\Audit;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '申请列表';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
$viewUrl = Url::toRoute('view');
$js = <<<JS
    $('.show-info').on('click',function () {
        $('#modal_id').find('.modal-title').html('申请表');
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

    <p>
        <?= Html::a('填写申请', ['apply'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            // 序号列
            ['class' => 'yii\grid\SerialColumn'],

            // 内容列
            'course.name',
            'typeStr',
            'statusStr',
            'apply_at:datetime',

            // 动作列
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function($url,$model,$key){
                        $options = [
                            'title'=>'查看',
                            'aria-label'=>'查看',
                            'class' => 'show-info',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_id',
                        ];
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>','#',$options);
                    },
                ],
            ],
        ],

        // 行样式设置
        'rowOptions' => function($model, $key, $index, $grid) {
            $color = [
                Audit::STATUS_FAILED => 'danger',
                Audit::STATUS_UNAUDITED => '',
                Audit::STATUS_PASS => 'success',
            ];
            return ['class' => $color[$model->status]];
        },

        'emptyText'=>'',
        'showOnEmpty'=>false,
    ]); ?>
</div>
