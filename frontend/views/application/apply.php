<?php

use common\models\Course;
use common\models\User;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model common\models\Application */
/* @var $form yii\widgets\ActiveForm */

$this->title = '停调课申请';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
$showCoursesUrl = Url::toRoute('/site/show-courses');
$freeClassroomUrl = Url::toRoute('free-classroom');
$js = <<<JS
    // 停调课类型下拉菜单事件
    $(document).on('change', '#application-type', function () {
        let suspend = $('#adjust-suspend');
        let schedule = $('#adjust-schedule');
        if($(this).val()==='1') {
            suspend.show();
            schedule.show();
        }     
        
        if($(this).val()==='2') {
            suspend.show();
            schedule.hide();
        }
        
        if($(this).val()==='3') {
            suspend.hide();
            schedule.show();
        }
    });

    // 显示课表按钮点击事件
    let modal = $('#modal_id');
    $(document).on('click', '#display-courses', function () {
        let week = $('#application-apply_week').val();        
        modal.find('.modal-title').html('我的课表');
        if (week) {    
            $.get('{$showCoursesUrl}', { week:week }, function (data) {
                modal.find('.modal-body').html(data);
            });
            
            // modal表格每个单元格点击事件
            $('.modal-body').on('click', 'td', function() {  
                if ($(this).text()) {
                    let dayArray = ['一', '二', '三', '四', '五', '六', '天'];
                    let dayStr = '星期'+dayArray[$(this).data('day')];
                    let secStart = $(this).data('sec');
                    let secStop = parseInt($(this).data('sec'))+parseInt($(this).attr('rowspan'))-1;
                    let courseStr = $(this).text().split(' ')[0];
                    $('#course-info').val(dayStr+' '+secStart+'-'+secStop+'节 '+courseStr);
                    $('#application-course_id').val($(this).data('id'));
                    modal.modal('hide');
                }
            });
            
        } else {
            modal.find('.modal-body').html('请先选择周');
        }
    });
    
    // 空闲教室按钮点击事件
    $(document).on('click', '#free-classroom', function () {
        let secCheckBox = [];
        $("#application-adjust_sec input[type='checkbox']").each(function () {
            if($(this).prop('checked')) {
                secCheckBox.push($(this).attr("value"));
            }
        });
        
        modal.find('.modal-title').html('空闲教室');
        $.post('{$freeClassroomUrl}', {course_id:1, day:$("#application-adjust_day").val(), sec:secCheckBox, week:$('#application-adjust_week').val()},
            function (data) {
                modal.find('.modal-body').html(data);
            }
        );
    });
    
    // modal表格每个单元格点击事件
    // $('.modal-body').on('click', 'td', function() {  
    //     // alert($(this).data('key'));
    //     alert($(this).text());
    // });
JS;

$this->registerJs($js);
?>

<div class="application-apply">

    <h3 align="center"><?= Html::encode($this->title) ?></h3>

    <div class="application-form">

        <?php $form = ActiveForm::begin(['enableAjaxValidation' => true, 'layout' => 'horizontal']); ?>

        <?= $form->field($model, 'type')->dropDownList([1=>'调课',2=>'停课',3=>'排课']) ?>

        <div id="adjust-suspend">
        <?= $form->field($model, 'apply_week')->dropDownList(Course::allWeeks(), ['prompt'=>'请选择']) ?>

        <?php $courseButton = Html::a('选择课程', '#', [
            'id'=>'display-courses',
            'data-toggle' => 'modal',
            'data-target' => '#modal_id',
            'class' => 'btn btn-default'
        ]) ?>
        </div>

        <?php
//        $form->field($model, 'course_id', [
//            'template'=>
//                "{label}
//                    <div class='col-sm-6'>
//                        <div class='input-group'>
//                            {input}
//                            <span class='input-group-btn'>".$courseButton."</span>
//                        </div>
//                        {hint}
//                        {error}
//                    </div>"
//        ])->textInput()
        ?>

        <div style="margin-bottom: 0;" class="form-group">
            <label class="control-label col-sm-3">课程</label>
            <div class='col-sm-6'>
                <div class='input-group'>
                    <input id='course-info' class='form-control' disabled>
                    <span class='input-group-btn'><?= $courseButton ?></span>
                </div>
            </div>
        </div>

        <?= $form->field($model, 'course_id', [
            'template'=>
                "<div class='col-sm-3'></div>
                    <div class='col-sm-6'>
                        <div class='input-group'>
                            {input}
                        </div>
                        {hint}
                        {error}
                    </div>"
        ])->hiddenInput()?>

        <div id="adjust-schedule">
            <?= $form->field($model, 'adjust_week')->dropDownList(Course::allWeeks(), ['prompt'=>'请选择']) ?>

            <?php $timeButton = Html::a('选择空闲时间段', '#', [
                'id'=>'free-time',
                'data-toggle' => 'modal',
                'data-target' => '#modal_id',
                'class' => 'btn btn-default'
            ]) ?>

            <?= $form->field($model, 'adjust_day')->dropDownList(Course::allDays(), ['prompt'=>'请选择']) ?>

            <?= $form->field($model, 'adjust_sec')->checkboxList(Course::allSections(), ['class'=>'form-inline']) ?>

            <?php $classroomButton = Html::a('显示空闲教室', '#', [
                    'id'=>'free-classroom',
                    'data-toggle' => 'modal',
                    'data-target' => '#modal_id',
                    'class' => 'btn btn-default'
            ]) ?>

            <?= $form->field($model, 'classroom_id', [
                'template'=>
                    "{label}
                    <div class='col-sm-6'>
                        <div class='input-group'>
                            {input}
                            <span class='input-group-btn'>".$classroomButton."</span>
                        </div>
                        {hint}
                        {error}
                    </div>"
                ])->textInput() ?>

            <?= $form->field($model, 'teacher_id')->dropDownList(User::allTeachers()) ?>
        </div>

        <?= $form->field($model, 'reason')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-6">
                <?= Html::submitButton('提交', ['class' => 'btn btn-success']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
