<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ClassroomSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '教室管理';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
$createUrl = Url::toRoute('create');
$updateUrl = Url::toRoute('update');
$js = <<<JS
    $('#create').on('click',function () {
        $('#modal_id').find('.modal-title').html('新增管理员');
        $.get('{$createUrl}', {}, function (data) {
                $('#modal_id').find('.modal-body').html(data);
            }
        );
    });

    $('.update').on('click',function () {
        $('#modal_id').find('.modal-title').html('修改资料');
        $.get('{$updateUrl}', { id:$(this).closest('tr').data('key') },
            function (data) {
                $('#modal_id').find('.modal-body').html(data);
            }
        );
    });
JS;
$this->registerJs($js);
?>

<div class="classroom-index">

    <p>
        <?= Html::a('新增教室', '#', [
            'class' => 'btn btn-success',
            'id'=>'create',
            'data-toggle' => 'modal',
            'data-target' => '#modal_id',
        ]) ?>
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
            ['attribute' => 'type',
             'value' => 'typeStr',
             'filter' => \common\models\Classroom::allTypes(),
            ],
            'amount',

            // 动作列
            ['class' => 'yii\grid\ActionColumn',
                'template'=>'{update} {delete}',
                'buttons' => [
                    'update' => function($url,$model,$key) {
                        $options = [
                            'title'=>'修改资料',
                            'aria-label'=>'修改资料',
                            'data-id' => $key,
                            'class' => 'update',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_id',
                        ];
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>','#',$options);
                    },
                ]
            ],
        ],
    ]); ?>
</div>
