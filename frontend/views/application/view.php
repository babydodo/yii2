<?php

use common\models\Audit;
use common\widgets\ApplyDetailWidget;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Application */

$this->title = '申请表详情';
$this->params['breadcrumbs'][] = ['label' => '申请列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="application-view">

    <?= ApplyDetailWidget::widget(['application'=>$model]) ?>

    <?php
    if ($model->status == Audit::STATUS_UNAUDITED) {
        echo '<p>';
        echo Html::a('撤销申请', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '你确定要撤销该申请吗?',
                'method' => 'post',
            ],
        ]);
        echo '</p>';
    }
    ?>

</div>
