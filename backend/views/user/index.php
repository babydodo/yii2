<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '用户管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('新增用户', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'username',
            'nickname',
            ['attribute'=>'className',
             'label'=>'班级',
             'value'=>'class.name',
            ],

            ['class' => 'yii\grid\ActionColumn',
             'template' => '{update} {resetpwd} {delete}',
             'buttons' => [
                 'resetpwd'=>function($url,$model,$key){
                     $options = [
                         'title'=>'重置密码',
                         'aria-label'=>'重置密码',
                     ];
                     return Html::a('<span class="glyphicon glyphicon-lock"></span>',$url,$options);
                 },
             ]
            ],
        ],
    ]); ?>
</div>
