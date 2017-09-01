<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use common\models\Adminuser;
use common\models\Audit;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Url;
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

    <?php
    // 自定义css
    $css = '.table-hover > tbody > tr:hover > td, .table-hover > tbody > tr:hover > th 
    {
        background-color: #dff0d8;
    }
    .btn-default:focus, .btn-default.focus 
    {
        background-color: #fff;
        border-color: #ccc;
    }';
    $this->registerCss($css);
    ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => '停调课管理系统-后台',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);

    // 系主任显示菜单
    if (!Yii::$app->user->isGuest && Yii::$app->user->identity->role == Adminuser::DIRECTOR) {
        $menuItems = [
            ['label' => '管理员管理', 'url' => ['/adminuser/index']],
            ['label' => '用户管理', 'url' => ['/user/index']],
            ['label' => '班级管理', 'url' => ['/classes/index']],
            ['label' => '教室管理', 'url' => ['/classroom/index']],
            ['label' => '课程管理', 'url' => ['/course/index']],
            ['label' => '设置', 'url' => ['/site/setting']],
        ];
    }

    // 其余管理员角色显示菜单
    if (!Yii::$app->user->isGuest) {
        $otherRoles = [Adminuser::DEAN, Adminuser::LABORATORY, Adminuser::COUNSELOR];
        if (in_array(Yii::$app->user->identity->role, $otherRoles, true)) {
            $menuItems = [
                '<li>'.
                Html::a('申请审核'.Audit::getUnauditedCount(Yii::$app->user->id),
                    ['/audit/index'],
                    ['class' => 'btn btn-link']
                ).
                '</li>',
            ];
        }
        // 辅导员角色增加课外活动菜单
        if (Yii::$app->user->identity->role == Adminuser::COUNSELOR) {
            $menuItems[] = ['label' => '课外活动', 'url' => ['/activity/index']];
        }
    }

    // 修改密码项 与 登陆/注销 项
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
