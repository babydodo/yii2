<?php

use common\widgets\CoursesWidget;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $courses common\models\Course */

$this->title = '班级课表';
$this->params['breadcrumbs'][] = ['label' => '班级管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="classes-show-courses">

		<div class="classes-courses">
		
		    <?= CoursesWidget::widget(['courses'=>$courses]) ?>
		
		</div>

</div>
