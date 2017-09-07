<?php

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \backend\models\LoginForm */

AppAsset::register($this);
$this->title = '停调课管理系统 - 后台';
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<body>
<?php $this->beginBody() ?>
<div class="wrap">
    <div  style="padding-top: 10%" class="col-sm-offset-4 col-lg-4">
    <div class="panel panel-default">
        <div class="panel-heading"><h3 class="panel-title text-center"><?= Html::encode($this->title) ?></h3></div>
        <div class="panel-body">
            <?php $form = ActiveForm::begin([
                    'layout' => 'horizontal',
                    'id' => 'login-form',
            ]); ?>

            <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

            <?= $form->field($model, 'password')->passwordInput() ?>

            <?= $form->field($model, 'rememberMe')->checkbox() ?>

            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-6">
                    <?= Html::submitButton('登陆', ['class' => 'btn btn-default', 'name' => 'login-button']) ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
    </div>

</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; 上海建桥学院 <?= date('Y') ?></p>

        <p class="pull-right">技术支持 <a href="#">半度微凉</a></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>
