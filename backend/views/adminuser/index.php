<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\AdminuserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '管理员';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="adminuser-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('新增管理员', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'username',
            'nickname',
            ['attribute'=>'role',
             'value' => 'roleStr',
             'filter' => \common\models\Adminuser::allRoles(),
            ],
            'email:email',

            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {resetpwd} {privilege} {delete}',
                'buttons' => [
                    'resetpwd' => function($url,$model,$key){
                        $options = [
                            'title'=>'重置密码',
                            'aria-label'=>'重置密码',
                        ];
                        return Html::a('<span class="glyphicon glyphicon-lock"></span>',$url,$options);
                    },
                    'privilege' => function($url,$model,$key){
                        $options = [
                            'title'=>'权限设置',
                            'aria-label'=>'权限设置',
                        ];
                        return Html::a('<span class="glyphicon glyphicon-user"></span>',$url,$options);
                    },
                ]
            ],
        ],
    ]); ?>
</div>
