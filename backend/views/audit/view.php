<?php

use common\models\Audit;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Application */
/* @var $id */

$this->title = '审核申请';
$this->params['breadcrumbs'][] = ['label' => '审核管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="application-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'application.user.nickname',
            'application.typeStr',
            'application.reason',
            'application.apply_at:datetime',
            // 调整前
            'application.course.name',
            'application.apply_week',
            'application.course.day',
            'application.course.sec',
            'application.course.classroom.name',
            // 调整后
            'application.adjust_week',
            'application.adjust_day',
            'application.adjust_sec',
            'application.classroom.name',
            'application.teacher.nickname',
        ],
    ]) ?>

    <?php if ($model->status== Audit::STATUS_UNAUDITED) { ?>
        <p>
            <?= Html::a('同意', ['pass', 'id' => $model->id], [
                    'class' => 'btn btn-primary',
                    'data' => [
                        'confirm' => '确定同意该申请吗?',
                        'method' => 'post',
                    ],
            ]) ?>

            <?= Html::a('不同意', ['failed', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => '确定不同意该申请吗?',
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    <?php } ?>

</div>
