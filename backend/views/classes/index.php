<?php

use common\models\Course;
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
$dropDownList = Html::dropDownList('week', null, Course::allWeeks(), ['class'=>"form-control", 'id'=>'drop-down-list']);
$dropDownList = '<div class="form-group form-horizontal">
                    <label class="control-label col-sm-4">周次</label>
                        <div class="col-sm-5">
                            ' .$dropDownList . '
                        </div>
                </div>';
$dropDownList = str_replace("\n", "\\\n", $dropDownList);
$createUrl = Url::toRoute('create');
$showCoursesUrl = Url::toRoute('show-courses');
$updateUrl = Url::toRoute('update');
$js = <<<JS
    $('#create').on('click',function () {
        $('#modal_id').find('.modal-title').html('新增管理员');
        $.get('{$createUrl}', {}, function (data) {
                $('#modal_id').find('.modal-body').html(data);
            }
        );
    });
    
    var class_id;
    $('.show-courses').on('click',function () {
        $('#modal_id').find('.modal-title').html('{$dropDownList}');
        class_id = $(this).closest('tr').data('key');
        $.get('{$showCoursesUrl}', { id:class_id },
            function (data) {
                $('#modal_id').find('.modal-body').html(data);
            }
        );
    });
    
    $(document).on('change', '#drop-down-list', function () {
        $.get('{$showCoursesUrl}', { id:class_id, week:$(this).val() }, function (data) {
            $('#modal_id').find('.modal-body').html(data);
        });
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

<div class="classes-index">

    <p>
        <?= Html::a('新增班级', '#', [
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
                            'class' => 'show-courses',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_id',
                        ];
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>','#',$options);
                    },

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
