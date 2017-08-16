<?php

use common\widgets\ApplyDetailWidget;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Application */

$this->title = '申请表详情';
$this->params['breadcrumbs'][] = ['label' => '申请列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="application-view">

    <p>
        <?= Html::a('撤销申请', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '你确定要撤销该申请吗?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= ApplyDetailWidget::widget(['application'=>$model]) ?>

</div>
