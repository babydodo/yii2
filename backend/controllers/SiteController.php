<?php
namespace backend\controllers;

use backend\models\ExcelUpload;
use backend\models\ResetpwdForm;
use common\models\Adminuser;
use Yii;
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
                        'actions' => ['index', 'resetpwd', 'logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    // 设置操作只允许系主任角色访问
                    [
                        'actions' => ['setting'],
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
     * 网站设置(待做)
     * @return string
     */
    public function actionSetting()
    {
        $model = new ExcelUpload();
        if (Yii::$app->request->isPost) {
            // 文件上传
            $model->file = UploadedFile::getInstance($model, 'file');
            // 文件上传存放的目录
            $dir = '../../uploads/'.date('Ymd');
            if (!is_dir($dir))
                mkdir($dir);
            if ($model->validate()) {
                // 文件名
                $baseName = iconv('UTF-8','gb2312', $model->file->baseName);
                $fileName = $baseName.'_'.date('His') . '.' . $model->file->extension;
                $dir = $dir.'/'. $fileName;
                $model->file->saveAs($dir);
            }

            // 导入Excel
            $file = $dir;
            if ($model->file->extension == 'xlsx') {
                $objReader = new \PHPExcel_Reader_Excel2007();
            } else {
                $objReader = new \PHPExcel_Reader_Excel5();
            }
            $objPHPExcel = $objReader->load($file);
            // 第一张表对象
            $objWorksheet = $objPHPExcel->getSheet(0);
            // 最大行数
            $highestRow = $objWorksheet->getHighestRow();
            // 最大列数(字母)
            $highestColumn = $objWorksheet->getHighestColumn();
            // 将最大列数变为数字
            $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);

            $tableData = [];
            // 方法1
            for($row = 2;$row<=$highestRow;$row++){
                // 写入数据库
//                $classroom = new Classroom();
//                $classroom->number = $objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
//                $classroom->name = $objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
//                $classroom->type = $objWorksheet->getCellByColumnAndRow(2, $row)->getValue();
//                $classroom->amount = $objWorksheet->getCellByColumnAndRow(3, $row)->getValue();
//                $classroom->save();
                for($col=0;$col< $highestColumnIndex;$col++){
                    $tableData[$row][$col] = $objWorksheet->getCellByColumnAndRow($col,$row)->getValue();
                }
            }

            // 方法2
            foreach ($objWorksheet->getRowIterator() as $row) { // 逐行读取
                foreach ($row->getCellIterator() as $item) { // 逐列读取
                    $cell = $item;
                }
            }

        }
        return $this->render('setting', [
            'model' => $model,
        ]);
    }

}
