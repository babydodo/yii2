<?php

use common\widgets\ApplyDetailWidget;

/* @var $this yii\web\View */
/* @var $model common\models\Application */

$this->title = '申请表详情';
$this->params['breadcrumbs'][] = ['label' => '申请列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="application-view">

    <?= ApplyDetailWidget::widget(['application'=>$model]) ?>

</div>
