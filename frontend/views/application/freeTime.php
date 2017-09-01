<?php

use common\models\Audit;
use common\widgets\ApplyDetailWidget;
use common\widgets\CoursesWidget;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $courses common\models\Course */

$this->title = '申请表详情';
$this->params['breadcrumbs'][] = ['label' => '申请列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="application-free-time">

    <?= CoursesWidget::widget(['courses'=>$courses, 'single'=>true]) ?>

    <div class="form-group">
        <?= Html::button('确定', ['id' => 'btn-confirm', 'class' => 'btn btn-primary', 'data-dismiss'=>'modal']) ?>
        <?= Html::button('取消', ['class' =>'btn btn-danger', 'data-dismiss'=>'modal']) ?>
    </div>

</div>
