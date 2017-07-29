<?php

use common\models\Classes;
use common\models\Course;
use common\models\User;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model backend\models\CourseForm */
/* @var $form yii\widgets\ActiveForm */
?>

<?php
$requestUrl = Url::toRoute('free-classroom');
$js = <<<JS
    $(document).on('click', '#btn_id', function () {
        var secCheckBox = [];
        $("#courseform-sec input[type='checkbox']").each(function () {
            if($(this).prop('checked')) {
                secCheckBox.push($(this).attr("value"));
            }
        });
        
        var weekCheckBox = [];
        $("#courseform-week input[type='checkbox']").each(function () {
            if($(this).prop('checked')) {
                weekCheckBox.push($(this).attr("value"));
            }
        });
        
        $.post('{$requestUrl}', {day:$("#courseform-day").val(), sec:secCheckBox, week:weekCheckBox},
            function (data) {
                $('#modal_id').find('.modal-body').html(data);
            }
        );
    });
JS;
$this->registerJs($js);
?>

<div class="course-form">

    <?php $form = ActiveForm::begin(['enableAjaxValidation' => true]); ?>

    <?= $form->field($model, 'number')->textInput() ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'user_id')->dropDownList(User::allTeachers(),['prompt'=>'请选择教师']) ?>

    <?= $form->field($model, 'day')->dropDownList(Course::allDays(),['prompt'=>'请选择时间']) ?>

    <?= $form->field($model, 'sec')->checkboxList(Course::allSections()) ?>

    <?= $form->field($model, 'week')->checkboxList(Course::allWeeks()) ?>

    <?= $form->field($model, 'classroom_id')->textInput() ?>

    <?= $form->field($model, 'classID')->checkboxList(Classes::allClasses(false)) ?>

    <?= Html::a('创建', '#', [
            'id' => 'btn_id',
            'data-toggle' => 'modal',
            'data-target' => '#modal_id',
            'class' => 'btn btn-success',
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新增' : '修改',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])
        ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php Modal::begin([
                'id' => 'modal_id',
                'header' => '<h4 class="modal-title">空闲教室</h4>',
                'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">确定</a>',
    ]); ?>

    <?php Modal::end(); ?>
    
</div>
