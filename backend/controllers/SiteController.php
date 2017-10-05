<?php
namespace backend\controllers;

use backend\models\ExcelUpload;
use backend\models\ResetpwdForm;
use common\models\Adminuser;
use common\models\Classes;
use common\models\Course;
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
     * 测试
     */
    public function actionTest()
    {
        $course = new Course();
        $course->number = '45678';
        $course->name = '测试';
        $course->user_id = 11;
        $course->week = [];
        $course->day = 2;
        $course->sec = [];
//                    $course->classroom_id = $course_info[4];
        $course->classroom_id = '';
        $course->validate();

    }

    /**
     *
     * @param array $arr
     * @return array
     */
    private function filter($arr) {
        // 处理day
        $day = ['','星期一','星期二','星期三','星期四','星期五','星期六','星期日'];
        $arr['day'] = array_search($arr[1], $day,true);

        // 处理sec
        $sec = explode('-', $arr[2]);
        if (empty($sec[1])) {
            $arr['sec'] = (int)$sec[0];
        } else {
            for ($i=(int)$sec[0];$i<=$sec[1];$i++) {
                $arr['sec'][] = $i;
            }
        }

        // 处理week
        preg_match('/(.*)\[(\d+)-?(\d*)\]/', trim($arr[3]), $match);
        $arr['week'] = [];
        if (empty($match[3])) {
            $arr['week'] = (int)$match[2];
        } elseif (empty($match[1])) {
            for ($j=(int)$match[2];$j<=(int)$match[3];$j++) {
                $arr['week'][] = $j;
            }
        } elseif ($match[1] == '单') {
            for ($j=(int)$match[2];$j<=(int)$match[3];$j++) {
                if ($j%2==1) {
                    $arr['week'][] = $j;
                }
            }
        } elseif ($match[1] == '双') {
            for ($j=(int)$match[2];$j<=(int)$match[3];$j++) {
                if ($j%2==0) {
                    $arr['week'][] = $j;
                }
            }
        }

        return $arr;
    }

}
