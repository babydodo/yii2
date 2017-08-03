<?php
namespace backend\controllers;

use backend\models\ResetpwdForm;
use common\models\Adminuser;
use Yii;
use yii\bootstrap\ActiveForm;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\LoginForm;
use yii\web\Response;

/**
 * 网站控制器
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
     * 显示首页
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * 修改密码
     */
    public function actionResetpwd()
    {
        $model = new ResetpwdForm();

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
     * @return string
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
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 注销
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
