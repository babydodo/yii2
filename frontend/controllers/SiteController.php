<?php
namespace frontend\controllers;

use common\models\Activity;
use common\models\Course;
use common\models\User;
use common\widgets\CoursesWidget;
use Yii;
use yii\bootstrap\ActiveForm;
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
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['index', 'resetpwd', 'logout', 'showCourses'],
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
     * 显示前台首页
     * @return string
     */
    public function actionIndex()
    {
//        return $this->render('index');
        return $this->redirect(['login']);
    }

    /**
     * 登陆
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
//            return $this->goHome();
            return $this->redirect(['show-courses']);
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
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * 显示个人详细课表
     * @param integer $week
     * @return CoursesWidget|string
     */
    public function actionShowCourses($week = 1)
    {
        // 筛选周
        $model = Course::find()->where('FIND_IN_SET('.$week.',week)');

        if (Yii::$app->user->identity->class_id == User::TEACHER_CLASS) {
            // 教师课表
            $showClasses = true;
            $courses = $model->andWhere(['user_id'=>Yii::$app->user->id])->all();
            $activities = [];
        } else {
            $showClasses = false;
            // part.1 学生课表
            $modelCopy = clone $model;
            $stuClassId = Yii::$app->user->identity->class_id;
            // 查询所属班级课程
            $classCourses = $model->innerJoinWith(['classes'])
                            ->andWhere(['classes.id' => $stuClassId])
                            ->all();
            // 查询学生自身选课
            $studentCourses = $modelCopy->innerJoinWith(['students'])
                            ->andWhere(['user.id'=>Yii::$app->user->id])
                            ->all();
            // 合并课程
            $courses = array_merge($classCourses, $studentCourses);

            // part.2 学生所属班级课外活动表
            $activities = Activity::find()
                ->where('FIND_IN_SET('.$week.',week)')
                ->andWhere('FIND_IN_SET('.$stuClassId.',classes_ids)')
                ->all();
        }

        // Ajax请求返回数据
        if (Yii::$app->request->isAjax) {
            return CoursesWidget::widget([
                'activities' => $activities,
                'courses'=>$courses,
                'showClasses' => $showClasses,
            ]);
        }

        return $this->render('showCourses', [
            'activities' => $activities,
            'courses' => $courses,
            'showClasses' => $showClasses,
        ]);
    }

    /**
     * 修改个人密码
     * @return boolean|string
     */
    public function actionResetpwd()
    {
        $model = new ResetpwdForm();

        // 块赋值与重置密码
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

}
