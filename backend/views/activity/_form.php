<?php

use common\models\Classes;
use common\models\Course;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model backend\models\CourseForm */
/* @var $form yii\widgets\ActiveForm */
?>

<?php
$freeClassroomUrl = Url::toRoute('free-classroom');
$js = <<<JS
    $(document).on('click', '#free-classroom', function () {
        $('#modal_id').find('.modal-title').html('空闲教室');

        let secCheckBox = [];
        $("#activity-sec input[type='checkbox']").each(function () {
            if($(this).prop('checked')) {
                secCheckBox.push($(this).attr("value"));
            }
        });
        
        let weekCheckBox = [];
        $("#activity-week input[type='checkbox']").each(function () {
            if($(this).prop('checked')) {
                weekCheckBox.push($(this).attr("value"));
            }
        });

        $.post('{$freeClassroomUrl}', {id:'{$model->id}', day:$("#activity-day").val(), sec:secCheckBox, week:weekCheckBox},
            function (data) {
                $('#modal_id').find('.modal-body').html(data);
            }
        );

        // modal每个按钮点击事件
        $('.modal-body').off('click').on('click', 'button', function() {
            $('#activity-classroom_id').val($(this).text());    
            $('#modal_id').modal('hide');
        });

    });
JS;
$this->registerJs($js);
?>

<div class="activity-form">

    <?php $form = ActiveForm::begin(['enableAjaxValidation' => true, 'layout' => 'horizontal']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'week')->checkboxList(Course::allWeeks(), [
        'item'=>function($index, $label, $name, $checked, $value) {
            $checkStr = $checked?'checked':'';
            $activeStr = $checked?' active':'';
            return '<div style="margin-bottom: 4px;" class="btn-group" data-toggle="buttons">
                        <label class="btn btn-default'.$activeStr.'">
                            <input type="checkbox" name="'.$name.'" value="'.$value.'" '.$checkStr.'>'
                            .$label.
                        '</label>
                    </div>';
        }]) ?>

    <?= $form->field($model, 'day')->dropDownList(Course::allDays(),['prompt'=>'请选择时间']) ?>

    <?= $form->field($model, 'sec')->checkboxList(Course::allSections(), [
        'item'=>function($index, $label, $name, $checked, $value) {
            $checkStr = $checked?'checked':'';
            $activeStr = $checked?' active':'';
            return '<div style="margin-bottom: 4px;" class="btn-group" data-toggle="buttons">
                        <label class="btn btn-default'.$activeStr.'">
                            <input type="checkbox" name="'.$name.'" value="'.$value.'" '.$checkStr.'>'
                            .$label.
                        '</label>
                    </div>';
        }]) ?>

    <?php $button = Html::a('显示空闲教室', '#', [
            'id' => 'free-classroom',
            'data-toggle' => 'modal',
            'data-target' => '#modal_id',
            'class' => 'btn btn-default',
    ]) ?>

    <?= $form->field($model, 'classroom_id', [
            'template'=>
                "{label}
                <div class='col-sm-6'>
                    <div class='input-group'>
                        {input}
                        <span class='input-group-btn'>".$button."</span>
                    </div>
                    {hint}
                    {error}
                </div>"
    ])->textInput() ?>

    <?= $form->field($model, 'classes_ids')->checkboxList(Classes::adminuserClasses(Yii::$app->user->id), [
        'item'=>function($index, $label, $name, $checked, $value) {
            $checkStr = $checked?'checked':'';
            $activeStr = $checked?' active':'';
            return '<div style="margin-bottom: 4px;" class="btn-group" data-toggle="buttons">
                        <label class="btn btn-default'.$activeStr.'">
                            <input type="checkbox" name="'.$name.'" value="'.$value.'" '.$checkStr.'>'
                            .$label.
                        '</label>
                    </div>';
        }]) ?>

    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-6">
            <?= Html::submitButton($model->isNewRecord ? '新增' : '修改',
                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])
            ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
    
</div>
