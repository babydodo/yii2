<?php

use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ClassesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '班级管理';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
$requestUrl = Url::toRoute('show-courses');
$js = <<<JS
    $('.courses').on('click',function () {
        $.get('{$requestUrl}', { id:$(this).closest('tr').data('key') },
            function (data) {
                $('#modal_id').find('.modal-body').html(data);
            }
        );
    });
JS;
$this->registerJs($js);
?>

<div class="classes-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('新增班级', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'number',
            'name',
            ['label'=>'辅导员',
             'attribute'=>'counselor',
             'value'=>'adminuser.nickname',
            ],

            ['class' => 'yii\grid\ActionColumn',
                'template' => '{show-courses} {update}',
                'buttons' => [
                    'show-courses' => function($url,$model,$key){
                        $options = [
                            'title'=>'显示课表',
                            'aria-label'=>'显示课表',
                            'data-id' => $key,
                            'class' => 'courses',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_id',
                        ];
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>','#',$options);
                    },

                ]
            ],
        ],
    ]); ?>

    <?php Modal::begin([
        'id' => 'modal_id',
        'header' => '<h4 class="modal-title">班级课表</h4>',
        'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">关闭</a>',
    ]); ?>

    <?php Modal::end(); ?>

</div>
