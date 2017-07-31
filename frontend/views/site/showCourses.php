<?php

use common\widgets\CoursesWidget;

/* @var $this yii\web\View */
/* @var $courses common\models\Course */

$this->title = '个人课表';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="classes-show-courses">

		<div class="classes-courses">
		
		    <?= CoursesWidget::widget(['courses'=>$courses]) ?>
		
		</div>

</div>
