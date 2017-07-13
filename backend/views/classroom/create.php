<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Classroom */

$this->title = '新增教室';
$this->params['breadcrumbs'][] = ['label' => '教室管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="classroom-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
