<?php

use common\models\Adminuser;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\AdminuserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '管理员';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
$createUrl = Url::toRoute('create');
$updateUrl = Url::toRoute('update');
$resetpwdUrl = Url::toRoute('resetpwd');
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

    $('.resetpwd').on('click',function () {
        $('#modal_id').find('.modal-title').html('重置密码');
        $.get('{$resetpwdUrl}', { id:$(this).closest('tr').data('key') },
            function (data) {
                $('#modal_id').find('.modal-body').html(data);
            }
        );
    });
JS;
$this->registerJs($js);
?>

<div class="adminuser-index">

    <p>
        <?= Html::a('新增管理员', '#', [
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
            'username',
            'nickname',
            ['attribute'=>'role',
             'value' => 'roleStr',
             'filter' => Adminuser::allRoles(),
            ],
            'email:email',

            // 动作列
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{update} {resetpwd} {delete}',
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
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', '#', $options);
                    },

                    'resetpwd' => function($url,$model,$key) {
                        $options = [
                            'title'=>'重置密码',
                            'aria-label'=>'重置密码',
                            'data-id' => $key,
                            'class' => 'resetpwd',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_id',
                        ];
                        return Html::a('<span class="glyphicon glyphicon-lock"></span>', '#', $options);
                    },
                ],
            ],
        ],
    ]); ?>

</div>
