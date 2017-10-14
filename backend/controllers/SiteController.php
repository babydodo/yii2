<?php
namespace backend\controllers;

use backend\models\ExcelUpload;
use backend\models\ResetpwdForm;
use common\models\Adminuser;
use Yii;
use yii\base\Model;
use yii\bootstrap\ActiveForm;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\LoginForm;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * 后台站点控制器
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['index', 'resetpwd', 'logout', 'test'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    // 设置操作只允许系主任角色访问
                    [
                        'actions' => [
                            'setting',
                            'upload-teachers',
                            'upload-classrooms',
                            'upload-adminusers',
                            'upload-students',
                            'upload-courses',
                            'file-download',
                        ],
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            if (!Yii::$app->user->isGuest) {
                                return Yii::$app->user->identity->role == Adminuser::DIRECTOR ? true : false;
                            }
                            return false;
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * 显示后台首页
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * 修改个人密码
     * @return boolean|string
     */
    public function actionResetpwd()
    {
        $model = new ResetpwdForm();

        // 块赋值与重置密码
        if ($model->load(Yii::$app->request->post()) && $model->resetPassword(Adminuser::findOne(Yii::$app->user->id))) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return true;
        } else {
            return $this->renderAjax('resetpwd', ['model' => $model]);
        }
    }

    /**
     * 验证重置密码表单
     * @return array
     */
    public function actionValidateResetpwd()
    {
        $model = new ResetpwdForm();
        $model->load(Yii::$app->request->post());
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ActiveForm::validate($model);
    }

    /**
     * 登陆
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $this->layout = false; //不使用布局
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 注销
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * 网站设置
     * @return Response|string
     */
    public function actionSetting()
    {
        $model = new ExcelUpload();
        return $this->render('setting', [
            'model' => $model,
        ]);
    }

    /**
     * 上传并导入教师用户
     * @return array|bool|string
     */
    public function actionUploadTeachers()
    {
        $model = new ExcelUpload();

        // 上传文件
        $fileDir = $this->uploadFile($model,'teacher');

        if ($fileDir) {

            // 导入数据
            $msg = $model->importTeacherUsers($fileDir);

            // 返回处理结果
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['success' => $msg];

        } else {
            return $this->render('setting', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 上传并导入教室数据
     * @return array|bool|string
     */
    public function actionUploadClassrooms()
    {
        $model = new ExcelUpload();

        // 上传文件
        $fileDir = $this->uploadFile($model,'classroom');

        if ($fileDir) {

            // 导入数据
            $msg = $model->importClassrooms($fileDir);

            // 返回处理结果
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['success' => $msg];

        } else {
            return $this->render('setting', [
                'model' => $model,
            ]);
        }

    }

    /**
     * 上传并导入辅导员及班级数据
     * @return array|bool|string
     */
    public function actionUploadAdminusers()
    {
        $model = new ExcelUpload();

        // 上传文件
        $fileDir = $this->uploadFile($model,'adminuser');

        if ($fileDir) {

            // 导入数据
            $msg = $model->importAdminusers($fileDir);

            // 返回处理结果
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['success' => $msg];

        } else {
            return $this->render('setting', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 上传并导入在校生数据
     * @return array|bool|string
     */
    public function actionUploadStudents()
    {
        $model = new ExcelUpload();

        // 上传文件
        $fileDir = $this->uploadFile($model,'student');

        if ($fileDir) {

            // 导入数据
            $msg = $model->importStudents($fileDir);

            // 返回处理结果
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['success' => $msg];

        } else {
            return $this->render('setting', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 上传并导入课程数据
     * @return array|bool|string
     */
    public function actionUploadCourses()
    {
        $model = new ExcelUpload();

        // 上传文件
        $fileDir = $this->uploadFile($model,'course');

        if ($fileDir) {

            // 导入数据
            $msg = $model->importCourses($fileDir);

            // 返回处理结果
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['success' => $msg];

        } else {
            return $this->render('setting', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 上传并导入管理员用户
     * @return array|bool|string
     */
    public function actionUploadAdmin()
    {
        $model = new ExcelUpload();

        // 上传文件
        $fileDir = $this->uploadFile($model,'admin');

        if ($fileDir) {

            // 导入数据
            $msg = $model->importAdmin($fileDir);

            // 返回处理结果
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['success' => $msg];

        } else {
            return $this->render('setting', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 文件上传
     * @param Model $model
     * @param string $attribute ExcelUpload模型属性
     * @return bool|string
     */
    protected function uploadFile($model, $attribute)
    {
        if (!Yii::$app->request->isAjax || empty($_FILES['ExcelUpload']['name'][$attribute])) {
            return false;
        }

        $model->$attribute = UploadedFile::getInstance($model, $attribute);

        // 文件类型验证
        if (!$model->validate()) {
            return false;
        }

        // 保存文件, 避免中文名, PHPExcel打开中文名文件有问题
        $dir = '../../uploads/' . date('Ymd');
        if (!is_dir($dir))
            mkdir($dir);
        $fileName = date('His') . '.' . $model->$attribute->extension;
        $fileDir = $dir . '/' . $fileName;
        return $model->$attribute->saveAs($fileDir) ? $fileDir : false;
    }

    /**
     * 文件下载
     * @param string $file 文件名
     * @return bool|Response
     */
    public function actionFileDownload($file)
    {
        //文件转码
        $file_name = iconv('utf-8', 'gb2312', $file);
        // 文件路径
        $file_path = '../../download/' . $file_name;
        // 判断文件是否存在
        if (!file_exists($file_path)) {
            Yii::$app->getSession()->setFlash('error', '文件不存在...');
            return $this->redirect(['site/setting']);
        }

        // 打开文件
        $fp = fopen($file_path, 'r');

        // 获取文件大小
        $file_size = filesize($file_path);

        // http响应头
        header('Content-type: application/octet-stream');   //返回的文件
        header('Accept-Ranges: bytes');                     //按照字节大小返回
        header('Accept-Length: ' . $file_size);             //返回文件大小
        header('Content-Disposition: attachment; filename=' . $file_name);    //客户端弹出的对话框对应的文件名

        // 向客户端返回数据
        $buffer = 1024;   // 设置输出大小
        // 为下载安全，设置文件字节读取计数器
        $file_count = 0;
        // 判断文件指针是否到了文件结束的位置
        while (!feof($fp) && ($file_size - $file_count) > 0) {
            $file_data = fread($fp, $buffer);
            // 统计读取多少个字节数
            $file_count += $buffer;
            // 把部分数据返回给浏览器
            echo $file_data;
        }
        // 关闭文件
        fclose($fp);
        return true;
    }

    /**
     * 测试
     */
    public function actionTest()
    {

    }


}
