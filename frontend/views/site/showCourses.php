<?php

use common\models\Course;
use common\widgets\CoursesWidget;
use yii\bootstrap\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $courses common\models\Course */

$this->title = '个人课表';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
$showCoursesUrl = Url::toRoute('show-courses');
$js = <<<JS
    // 周次下拉菜单事件
    $(document).on('change', '#drop-down-list', function () {
        $.get('{$showCoursesUrl}', { week:$(this).val() }, function (data) {
            $('#courses').html(data);
        });
    });
JS;

$this->registerJs($js);
?>

<div class="classes-show-courses">

    <div class="classes-courses">

        <div class="row form-group form-horizontal">
            <label class="control-label col-sm-1">周次</label>
            <div class="col-sm-2">
                <?= Html::dropDownList('week', null, Course::allWeeks(), [
                        'class'=>"form-control",
                        'id'=>'drop-down-list',
                        'autocomplete'=>'off']
                ) ?>
            </div>
        </div>

        <div id="courses">
            <?= CoursesWidget::widget(['courses'=>$courses]) ?>
        </div>

    </div>

</div>
