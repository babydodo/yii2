<?php

use common\models\Audit;
use common\widgets\ApplyDetailWidget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Audit */
/* @var $id */

$this->title = '审核申请';
$this->params['breadcrumbs'][] = ['label' => '审核管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
$passUrl = Url::toRoute(['pass', 'id' => $model->id]);
$failedUrl = Url::toRoute(['failed', 'id' => $model->id]);
$js = <<<JS
    $('#agree').on('click',function () {
        $("#remark-from").attr('action', '{$passUrl}');
    });

    $('#disagree').on('click',function () {
        $("#remark-from").attr('action', '{$failedUrl}');
    });
JS;
$this->registerJs($js);
?>

<div class="audit-view">

    <?= ApplyDetailWidget::widget([
            'application' => $model->application,
            'showProgress' => false,
    ]) ?>

    <?php if ($model->application->status == Audit::STATUS_UNAUDITED) {

        ActiveForm::begin(['id'=>'remark-from', 'action'=>null]); ?>

        <div class="form-group">
            <?= Html::activeTextarea($model, 'remark', [
                    'class' => 'form-control',
                    'rows' => '3',
                    'placeholder' => '审核意见',
            ])?>
        </div>

        <div class="form-group">
            <?= Html::submitButton('同意', [
                'id' => 'agree',
                'class' => 'btn btn-primary',
                'data' => [
                    'confirm' => '确定同意该申请吗?',
                ],
            ]) ?>

            <?= Html::submitButton('不同意', [
                'id' => 'disagree',
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => '确定不同意该申请吗?',
                ],
            ]) ?>
        </div>

        <?php ActiveForm::end();

    } ?>

</div>
