<?php
namespace frontend\controllers;

use common\models\Course;
use common\models\User;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\models\LoginForm;
use backend\models\ResetpwdForm;

/**
 * Site controller
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
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
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
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * 显示主页
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * 登陆
     *
     * @return mixed
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
     * 注销当前用户
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * 显示个人详细课表
     * @return mixed
     */
    public function actionShowCourses()
    {
        // 筛选周
        $model = Course::find()->where('FIND_IN_SET(3,week)');

        if (Yii::$app->user->identity->class_id == User::TEACHER_CLASS) {
            // 教师课表
            $courses = $model->andWhere(['user_id'=>Yii::$app->user->id])->all();
        } else {
            // 学生课表
            $courses = $model->joinWith(['classes', 'students'])
                            ->andWhere(['user.id'=>Yii::$app->user->id])
                            ->orWhere(['classes.id' => Yii::$app->user->identity->class_id])
                            ->all();
        }

        return $this->render('showCourses', ['courses' => $courses]);
    }

    /**
     * Resets password.
     *
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetpwd()
    {
        $model = new ResetpwdForm();
        $user = User::findOne(Yii::$app->user->id);
        if ($model->load(Yii::$app->request->post()) && $model->resetPassword($user)) {
            Yii::$app->session->setFlash('success', '密码修改成功!');
            return $this->goHome();
        } else {
            return $this->render('resetpwd', ['model' => $model]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }


}
