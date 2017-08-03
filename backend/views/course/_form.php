<?php

use common\models\Classes;
use common\models\Course;
use common\models\User;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
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
        
        $('#modal_id').find('.modal-title').html('空闲教室');
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

    <?php $form = ActiveForm::begin(['enableAjaxValidation' => true, 'layout' => 'horizontal']); ?>

    <?= $form->field($model, 'number')->textInput() ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'user_id')->dropDownList(User::allTeachers(),['prompt'=>'请选择教师']) ?>

    <?= $form->field($model, 'day')->dropDownList(Course::allDays(),['prompt'=>'请选择时间']) ?>

    <?= $form->field($model, 'sec')->checkboxList(Course::allSections(), ['class'=>'form-inline']) ?>

    <?= $form->field($model, 'week')->checkboxList(Course::allWeeks(), ['class'=>'form-inline']) ?>

    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-6">
            <?= Html::a('显示空闲教室', '#', [
                    'id' => 'btn_id',
                    'data-toggle' => 'modal',
                    'data-target' => '#modal_id',
                    'class' => 'btn btn-default',
            ]) ?>
        </div>
    </div>

    <?= $form->field($model, 'classroom_id')->textInput() ?>

    <?= $form->field($model, 'classID')->checkboxList(Classes::allClasses(false), ['class'=>'form-inline']) ?>

    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-6">
            <?= Html::submitButton($model->isNewRecord ? '新增' : '修改',
                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])
            ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
    
</div>
