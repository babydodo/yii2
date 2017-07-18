<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
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

    // 系主任显示菜单
    if (!Yii::$app->user->isGuest && Yii::$app->user->identity->role == 1) {
        $menuItems = [
            ['label' => '管理员管理', 'url' => ['/adminuser/index']],
            ['label' => '用户管理', 'url' => ['/user/index']],
            ['label' => '班级管理', 'url' => ['/classes/index']],
            ['label' => '教室管理', 'url' => ['/classroom/index']],
            ['label' => '课程管理', 'url' => ['/course/index']],
        ];
    }

    // 其余角色显示菜单
    if (!Yii::$app->user->isGuest && in_array(Yii::$app->user->identity->role, [2,3,4], true) ) {
        $menuItems = [
            ['label' => '申请审核', 'url' => ['/application/index']],
        ];
    }

    // 登陆与注销项
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => '登陆', 'url' => ['/site/login']];
    } else {
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
