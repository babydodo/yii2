<?php
namespace frontend\controllers;

use common\models\Course;
use common\models\User;
use common\widgets\CoursesWidget;
use Yii;
use yii\bootstrap\ActiveForm;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\models\LoginForm;
use backend\models\ResetpwdForm;
use yii\web\Response;


/**
 * 前台站点控制器
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
     * @param int $week
     * @return mixed
     */
    public function actionShowCourses($week = 1)
    {
        // 筛选周
        $model = Course::find()->where('FIND_IN_SET('.$week.',week)');

        if (Yii::$app->user->identity->class_id == User::TEACHER_CLASS) {
            // 教师课表
            $courses = $model->andWhere(['user_id'=>Yii::$app->user->id])->all();
        } else {
            // 学生课表
            $model2 = clone $model;
            // step.1 查询所属班级课程
            $classCourses = $model->innerJoinWith(['classes'])
                            ->andWhere(['classes.id' => Yii::$app->user->identity->class_id])
                            ->all();
            // step.2 查询学生自身选课
            $studentCourses = $model2->innerJoinWith(['students'])
                            ->andWhere(['user.id'=>Yii::$app->user->id])
                            ->all();
            // step.3 合并课程
            $courses = array_merge($classCourses, $studentCourses);
        }

        if (Yii::$app->request->isAjax) {
            return CoursesWidget::widget(['courses'=>$courses]);
        }

        return $this->render('showCourses', ['courses' => $courses]);
    }

    /**
     * 重置密码
     *
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetpwd()
    {
        $model = new ResetpwdForm();
        if ($model->load(Yii::$app->request->post()) && $model->resetPassword(User::findOne(Yii::$app->user->id))) {
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
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

}
