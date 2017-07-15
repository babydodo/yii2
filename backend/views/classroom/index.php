<?php

use yii\helpers\Html;
use yii\grid\GridView;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\ClassroomSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '教室管理';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="classroom-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('新增教室', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'number',
            'name',
            ['label' => '类型',
             'attribute' => 'type',
             'value' => 'typeStr',
             'filter' => \common\models\Classroom::allTypes(),
            ],
            'amount',

            ['class' => 'yii\grid\ActionColumn',
             'template'=>'{update} {delete}',
            ],
        ],
    ]); ?>
</div>
