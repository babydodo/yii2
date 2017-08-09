<?php

/* @var $this \yii\web\View */
/* @var $content string */

use common\models\User;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;

AppAsset::register($this);
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

    <?php
    // 自定义js
    $resetpwdUrl = Url::toRoute('/site/resetpwd');
    $js = <<<JS
    $('#resetpwd').on('click',function () {
        $('#modal_id').find('.modal-title').html('重置密码');
        $.get('{$resetpwdUrl}', {},
            function (data) {
                $('#modal_id').find('.modal-body').html(data);
            }
        );
    });    
JS;
    $this->registerJs($js);
    ?>

</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => '停调课管理系统',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);

    if (!Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => '个人课表', 'url' => ['/site/show-courses']];
        if (Yii::$app->user->identity->class_id == User::TEACHER_CLASS) {
            $menuItems[] = ['label' => '停调课申请', 'url' => ['/application/index']];
        }
    }

    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => '登陆', 'url' => ['/site/login']];
    } else {
        $menuItems[] = '<li>'
            . Html::a('修改密码', '#', [
                'id'=>'resetpwd',
                'data-toggle' => 'modal',
                'data-target' => '#modal_id',
                'class' => 'btn btn-link'
            ])
            . '</li>';
        $menuItems[] = '<li>'
            . Html::beginForm(['/site/logout'], 'post')
            . Html::submitButton(
                '注销 (' . Yii::$app->user->identity->nickname . ')',
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
            . '</li>';
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<?php Modal::begin([
    'id' => 'modal_id',
    'header' => '<h4 class="modal-title"></h4>',
]); ?>

<?php Modal::end(); ?>

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
