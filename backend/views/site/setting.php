<?php

use kartik\file\FileInput;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model \backend\models\ExcelUpload  */

$this->title = '数据导入';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-setting">

    <div id="msg"></div>

    <h3 align="center">数据导入</h3>

    <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'options' => [
                'enctype' => 'multipart/form-data'
            ],
    ]) ?>

    <div class="form-group">
        <label class="control-label col-sm-3">样本下载</label>
        <div class='col-sm-6'>
            <?= Html::a('教师名单', ['/site/file-download', 'file'=>'教师名单.xls'], ['class' =>'btn btn-default']) ?>
            <?= Html::a('教室列表', ['/site/file-download', 'file'=>'教室列表.xls'], ['class' =>'btn btn-default']) ?>
            <?= Html::a('辅导员&班级', ['/site/file-download', 'file'=>'辅导员&班级.xls'], ['class' =>'btn btn-default']) ?>
            <?= Html::a('学生名单', ['/site/file-download', 'file'=>'学生名单.xls'], ['class' =>'btn btn-default']) ?>
            <?= Html::a('课程列表', ['/site/file-download', 'file'=>'课程列表.xls'], ['class' =>'btn btn-default']) ?>
            <?= Html::a('管理员名单', ['/site/file-download', 'file'=>'管理员名单.xls'], ['class' =>'btn btn-default']) ?>
        </div>
    </div>

    <?= $form->field($model, 'teacher')->widget(FileInput::classname(), [
        // 插件配置
        'pluginOptions' => [
            // 异步上传的接口地址
            'uploadUrl' => Url::toRoute(['/site/upload-teachers']),
            // 是否显示预览区域
            'showPreview' => false,
            // 允许接收的文件后缀
            'allowedFileExtensions' => ['xlsx', 'xls'],
        ],
        // 事件行为
        'pluginEvents' => [
            // 上传成功后的回调方法
            'fileuploaded' => "function (event, data, id, index) {
                let msg = '<div class=\"alert-info alert fade in\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">×</button>'
                + data.response.success.success + '<p>' + data.response.success.error + '</p></div>';
                $('#msg').html(msg);
            }",
        ],
    ]) ?>

    <?= $form->field($model, 'classroom')->widget(FileInput::classname(), [
        // 插件配置
        'pluginOptions' => [
            // 异步上传的接口地址
            'uploadUrl' => Url::toRoute(['/site/upload-classrooms']),
            // 是否显示预览区域
            'showPreview' => false,
            // 允许接收的文件后缀
            'allowedFileExtensions' => ['xlsx', 'xls'],
        ],
        // 事件行为
        'pluginEvents' => [
            // 上传成功后的回调方法
            'fileuploaded' => "function (event, data, id, index) {
                let msg = '<div class=\"alert-info alert fade in\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">×</button>'
                + data.response.success.success + '<p>' + data.response.success.error + '</p></div>';
                $('#msg').html(msg);
            }",
        ],
    ]) ?>

    <?= $form->field($model, 'adminuser')->widget(FileInput::classname(), [
        // 插件配置
        'pluginOptions' => [
            // 异步上传的接口地址
            'uploadUrl' => Url::toRoute(['/site/upload-adminusers']),
            // 是否显示预览区域
            'showPreview' => false,
            // 允许接收的文件后缀
            'allowedFileExtensions' => ['xlsx', 'xls'],
        ],
        // 事件行为
        'pluginEvents' => [
            // 上传成功后的回调方法
            'fileuploaded' => "function (event, data, id, index) {
                let msg = '<div class=\"alert-info alert fade in\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">×</button>'
                + data.response.success.success + '<p>' + data.response.success.error + '</p></div>';
                $('#msg').html(msg);
            }",
        ],
    ]) ?>

    <?= $form->field($model, 'student')->widget(FileInput::classname(), [
        // 插件配置
        'pluginOptions' => [
            // 异步上传的接口地址
            'uploadUrl' => Url::toRoute(['/site/upload-students']),
            // 是否显示预览区域
            'showPreview' => false,
            // 允许接收的文件后缀
            'allowedFileExtensions' => ['xlsx', 'xls'],
        ],
        // 事件行为
        'pluginEvents' => [
            // 上传成功后的回调方法
            'fileuploaded' => "function (event, data, id, index) {
                let msg = '<div class=\"alert-info alert fade in\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">×</button>'
                + data.response.success.success + '<p>' + data.response.success.error + '</p></div>';
                $('#msg').html(msg);
            }",
        ],
    ]) ?>

    <?= $form->field($model, 'course')->widget(FileInput::classname(), [
        // 插件配置
        'pluginOptions' => [
            // 异步上传的接口地址
            'uploadUrl' => Url::toRoute(['/site/upload-courses']),
            // 是否显示预览区域
            'showPreview' => false,
            // 允许接收的文件后缀
            'allowedFileExtensions' => ['xlsx', 'xls'],
        ],
        // 事件行为
        'pluginEvents' => [
            // 上传成功后的回调方法
            'fileuploaded' => "function (event, data, id, index) {
                let msg = '<div class=\"alert-info alert fade in\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">×</button>'
                + data.response.success.success + '<p>' + data.response.success.error + '</p></div>';
                $('#msg').html(msg);
            }",
        ],
    ]) ?>

    <?= $form->field($model, 'admin')->widget(FileInput::classname(), [
        // 插件配置
        'pluginOptions' => [
            // 异步上传的接口地址
            'uploadUrl' => Url::toRoute(['/site/upload-admin']),
            // 是否显示预览区域
            'showPreview' => false,
            // 允许接收的文件后缀
            'allowedFileExtensions' => ['xlsx', 'xls'],
        ],
        // 事件行为
        'pluginEvents' => [
            // 上传成功后的回调方法
            'fileuploaded' => "function (event, data, id, index) {
                let msg = '<div class=\"alert-info alert fade in\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">×</button>'
                + data.response.success.success + '<p>' + data.response.success.error + '</p></div>';
                $('#msg').html(msg);
            }",
        ],
    ]) ?>

    <?php ActiveForm::end() ?>

</div>
