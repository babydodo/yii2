<?php

use common\models\Application;
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
$allCoursesUrl = Url::toRoute('all-courses');
$freeTimeUrl = Url::toRoute('free-time');
$freeClassroomUrl = Url::toRoute('free-classroom');
$js = <<<JS
    let modal = $('#modal_id');
    let modal_body = $('.modal-body');
    let apply_week = $('#application-apply_week');
    let apply_sec = $('#application-apply_sec');
    let course_id = $('#application-course_id');
    let adjust_week = $('#application-adjust_week');
    let adjust_day = $('#application-adjust_day');
    let adjust_sec = $('#application-adjust_sec');
    let course_info = $('#course-info');
    let time_info = $('#time-info');
    let dayArray = ['', '一', '二', '三', '四', '五', '六', '天'];
    
    // 停调课类型下拉菜单事件
    $('#application-type').on('change', function () {
        let suspend = $('#adjust-suspend');
        let schedule = $('#adjust-schedule');
        
        // 如果选择调课
        if($(this).val()==='1') {
            suspend.show();
            schedule.show();
        }
        
        // 如果选择停课
        if($(this).val()==='2') {
            suspend.show();
            schedule.hide();
            
        }
        // 如果选择排课
        if($(this).val()==='3') {
            suspend.hide();
            schedule.show();
            
        }
       
        course_info.val('');
        course_id.val('');
        apply_sec.val('');
    });

    // 调整前周次下拉菜单事件
    $(apply_week).on('change', function () {
        course_info.val('');
        course_id.val('');
        apply_sec.val('');
    });
    
    // 显示课表按钮点击事件
    $('#display-courses').on('click', function () {
        modal.find('.modal-title').html('我的课表');
        // 如果是排课
        if ($('#application-type').val() === '3') {
            $.get('{$allCoursesUrl}', {}, function (data) {
                modal.find(modal_body).html(data);
            });
            // 每个课程名按钮点击事件
            $(modal_body).off('click').on('click', 'button', function() {
                course_info.val($(this).text());
                course_id.val($(this).data('key'));
                modal.modal('hide');
            });
        } else {
            // 如果不是排课
            if (apply_week.val()) {    
                $.get('{$showCoursesUrl}', { week:apply_week.val() }, function (data) {
                    modal.find(modal_body).html(data);
                });
                
                // modal表格每个单元格点击事件
                $(modal_body).off('click').on('click', 'td', function() {  
                    if ($(this).text()) {                  
                        let dayStr = '星期'+dayArray[$(this).data('day')];
                        let secStart = $(this).data('sec');
                        let secStop = parseInt($(this).data('sec'))+parseInt($(this).attr('rowspan'))-1;
                        let secStr = '';
                        if (secStart !== secStop) {
                            for (let i=secStart;i<secStop;i++) {
                                secStr += i+',';
                            }
                        }
                        secStr += secStop;
                        let courseStr = $(this).text().split(' ')[0];
                        course_info.val(dayStr+' '+secStart+'-'+secStop+'节 '+courseStr);
                        course_id.val($(this).data('id'));
                        apply_sec.val(secStr);
                        modal.modal('hide');
                    }
                });
                
            } else {
                modal.find(modal_body).html('请先选择 需调整周次');
            }
        }
    });
    
    // 显示空闲时间段按钮点击事件
    $('#free-time').on('click', function () {
        modal.find('.modal-title').html('请选择时间段');
        
        if (course_id.val() && adjust_week.val()) {
            $.post('{$freeTimeUrl}', 
                {
                    apply_week:apply_week.val(), 
                    course_id:course_id.val(),
                    apply_sec:apply_sec.val(),
                    teacher_id:$('#application-teacher_id').val(),
                    adjust_week:adjust_week.val()
                }, 
                function (data) {
                    modal.find(modal_body).html(data);
            });
            
            // modal表格每个单元格点击事件
            let secArray = [];
            let day = '';
            
            let adjustSec = '';
            let timeStr = '';
            $(modal_body).off('click').on('click', 'td', function() {  
                if ($(this).text()==='') {
                    // 限制选择的时段在同一天
                    if ($(this).hasClass('info')) {
                        $(this).removeClass('info');
                        secArray.splice($.inArray($(this).data('sec'), secArray), 1);
                        if (secArray.length === 0) {
                            day = '';
                        }
                    } else if(day === $(this).data('day') || day==='') {
                        day = $(this).data('day');
                        $(this).addClass('info');
                        secArray.push($(this).data('sec'));
                    }
                    
                    adjustSec = secArray.sort( function(a, b){return a-b;} ).toString();
                    timeStr = adjustSec===''?'':'星期' + dayArray[day] + '('+adjustSec+')节';
                }
            });
            
            // 确定按钮点击事件
            $(modal_body).on('click', '#btn-confirm', function() {
                time_info.val(timeStr);
                adjust_day.val(day);
                adjust_sec.val(adjustSec);
            });
            
        } else {
            modal.find(modal_body).html('请先选择 调整课程 与 周次');
        }
    });
    
    // 空闲教室按钮点击事件
    $('#free-classroom').on('click', function () {
        modal.find('.modal-title').html('空闲教室');
        if (adjust_week.val() && adjust_sec.val()) {
            $.post('{$freeClassroomUrl}',
                {
                    course_id:course_id.val(),
                    week:adjust_week.val(),
                    day:adjust_day.val(),
                    sec:adjust_sec.val(),
                },
                function (data) {
                    modal.find(modal_body).html(data);
                }
            );
            
            // modal每个按钮点击事件
            $(modal_body).off('click').on('click', 'button', function() {
                $('#application-classroom_id').val($(this).text());    
                modal.modal('hide');
            });
        } else {
            modal.find(modal_body).html('请先选择 调整后周次 与 时间段');
        }
    });
    
    // 事由快捷选择下拉菜单
    $('.dropdown-menu-right').on('click', 'a', function() {
        $('#application-reason').val($(this).text());
    });

JS;

$this->registerJs($js);
?>

<div class="application-apply">

    <h3 align="center"><?= Html::encode($this->title) ?></h3>

    <div class="application-form">

        <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

        <?= $form->field($model, 'type')->dropDownList(Application::allTypes(), ['autocomplete'=>'off']) ?>

        <div id="adjust-suspend">

            <?= $form->field($model, 'apply_week')->dropDownList(Course::allWeeks(), [
                    'prompt'=>'请选择',
                    'autocomplete'=>'off'
            ]) ?>

            <?php $courseButton = Html::a('选择课程', '#', [
                'id'=>'display-courses',
                'data-toggle' => 'modal',
                'data-target' => '#modal_id',
                'class' => 'btn btn-default',
            ]) ?>

        </div>

        <div style="margin-bottom: 0;" class="form-group">
            <label class="control-label col-sm-3">调整课程</label>
            <div class='col-sm-6'>
                <div class='input-group'>
                    <input id='course-info' class='form-control' autocomplete='off' readOnly>
                    <span class='input-group-btn'><?= $courseButton ?></span>
                </div>
            </div>
        </div>

        <?= $form->field($model, 'course_id', [
            'template'=>
                "<div class='col-sm-offset-3 col-sm-6'>
                    <div class='input-group'>
                        {input}
                    </div>
                    {hint}
                    {error}
                </div>"
        ])->hiddenInput()?>

        <?= Html::activeHiddenInput($model, 'apply_sec') ?>

        <div id="adjust-schedule">

            <?= $form->field($model, 'teacher_id')->dropDownList(User::allTeachers()) ?>

            <?= $form->field($model, 'adjust_week')->dropDownList(Course::allWeeks(), [
                    'prompt'=>'请选择',
                    'autocomplete'=>'off'
            ]) ?>

            <?php $freeTimeButton = Html::a('选择时间段', '#', [
                'id'=>'free-time',
                'data-toggle' => 'modal',
                'data-target' => '#modal_id',
                'class' => 'btn btn-default',
            ]) ?>

            <div style="margin-bottom: 0;" class="form-group">
                <label class="control-label col-sm-3">调整后时间段</label>
                <div class='col-sm-6'>
                    <div class='input-group'>
                        <input id='time-info' class='form-control' readOnly>
                        <span class='input-group-btn'><?= $freeTimeButton ?></span>
                    </div>
                </div>
            </div>

            <?= Html::activeHiddenInput($model, 'adjust_day') ?>

            <?= $form->field($model, 'adjust_sec', [
                'template'=>
                    "<div class='col-sm-offset-3 col-sm-6'>
                        <div class='input-group'>
                            {input}
                        </div>
                        {hint}
                        {error}
                    </div>"
            ])->hiddenInput() ?>

            <?php $classroomButton = Html::a('显示空闲教室', '#', [
                    'id'=>'free-classroom',
                    'data-toggle' => 'modal',
                    'data-target' => '#modal_id',
                    'class' => 'btn btn-default',
            ]) ?>

            <?= $form->field($model, 'classroom_id', [
                'enableAjaxValidation' => true,
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

        </div>

        <?= $form->field($model, 'reason', [
            'template'=>
                "{label}
                <div class='col-sm-6'>
                    <div class='input-group'>
                        {input}
                        <div class='input-group-btn'>
                            <button type='button' class='btn btn-default dropdown-toggle' data-toggle='dropdown'>
                                快捷选择
                                <span class='caret'></span>
                            </button>
                            <ul class='dropdown-menu dropdown-menu-right'>
                                <li><a href='#'>因公出差</a></li>
                                <li><a href='#'>因病请假</a></li>
                                <li><a href='#'>因事请假</a></li>
                                <li><a href='#'>课时不足</a></li>
                            </ul>
                        </div>
                    </div>
                    {hint}
                    {error}
                </div>"
        ])->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'remark')->textarea(['maxlength' => true, 'placeholder'=>'选填']) ?>

        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-6">
                <?= Html::submitButton('提交', ['class' => 'btn btn-success']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
