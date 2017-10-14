<?php

namespace frontend\controllers;

use common\models\Activity;
use common\models\Adminuser;
use common\models\Application;
use common\models\Audit;
use common\models\Classroom;
use common\models\Course;
use common\models\User;
use common\widgets\ButtonsWidget;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * 停调课申请审核控制器
 */
class ApplicationController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        // 控制器只允许教师角色访问
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            if (!Yii::$app->user->isGuest) {
                                return Yii::$app->user->identity->class_id == User::TEACHER_CLASS ? true : false;
                            }
                            return false;
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * 列出所有申请记录
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Application::find()
                ->where(['user_id'=>Yii::$app->user->id])
                ->orderBy(['apply_at' => SORT_DESC]),
            'pagination' => ['defaultPageSize' => 10],  //分页
        ]);
        $dataProvider->setSort(false);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 停调课申请
     * @return array|string
     * @throws \Exception
     */
    public function actionApply()
    {
        $model = new Application();
        $model->teacher_id = Yii::$app->user->id;

        // 块赋值
        $load = $model->load(Yii::$app->request->post());
        $model->user_id = Yii::$app->user->id;

        // Ajax验证
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($load && $model->validate()) {
            // 开启事务
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model->save(false);
                // 推送给相应管理员
                $this->push($model);
                // 提交事务
                $transaction->commit();
                Yii::$app->getSession()->setFlash('success', '申请成功, 请等待审核...');
                return $this->redirect(['index']);
            } catch (\Exception $e) {
                // 回滚
                $transaction->rollback();
                throw $e;
            }
        } else {
            return $this->render('apply', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 根据时间查询空闲教室
     * @return string
     */
    public function actionFreeClassroom()
    {
        $request = Yii::$app->request;
        $course_id = $request->post('course_id');
        $day = $request->post('day');
        $sec = $request->post('sec');
        $week = $request->post('week');

        // Course表中某一时间段使用的教室
        $usedClassroom1 = Course::find()
            ->select(['classroom_id'])
            ->andFilterWhere(['not', ['id'=>$course_id]])
            ->andWhere('FIND_IN_SET('.$week.',week)')
            ->andWhere(['day'=>$day])
            ->andWhere("CONCAT(',',`sec`,',') REGEXP '[^0-9]+(".$sec.")[^0-9]+'")
            ->column();

        // Application表中某一时间段使用的教室
        $usedClassroom2 = Application::find()
            ->select(['classroom_id'])
            ->andWhere(['status' => Audit::STATUS_UNAUDITED])
            ->andWhere('FIND_IN_SET('.$week.',adjust_week)')
            ->andWhere(['adjust_day'=>$day])
            ->andWhere("CONCAT(',',`adjust_sec`,',') REGEXP '[^0-9]+(".$sec.")[^0-9]+'")
            ->column();

        // Activity表中某一时间段使用的教室
        $usedClassroom3 = Activity::find()
            ->select(['classroom_id'])
            ->andWhere('FIND_IN_SET('.$week.',week)')
            ->andWhere(['day'=>$day])
            ->andWhere("CONCAT(',',`sec`,',') REGEXP '[^0-9]+(".$sec.")[^0-9]+'")
            ->column();

        // 合并为某一时间段中使用或者被申请的教室
        $usedClassroom = array_merge($usedClassroom1, $usedClassroom2, $usedClassroom3);

        // 某一时间段中未被占用的教室
        $freeClassroom = Classroom::find()->where(['not in', 'id', $usedClassroom])->all();

        return ButtonsWidget::widget(['classrooms'=>$freeClassroom]);

    }

    /**
     * 根据调整后周次显示课表空闲时间段
     * @return string
     */
    public function actionFreeTime()
    {
        $request = Yii::$app->request;
        $apply_week = $request->post('apply_week');
        $course_id = $request->post('course_id');
        $apply_sec = $request->post('apply_sec');
        $teacher_id = $request->post('teacher_id');
        $adjust_week = $request->post('adjust_week');

        // part.1 Course表
        $course = Course::find();

        // 如果调整后周次不变, 则排除申请课程本身以便可以选择课程本身的时间段
        $adjustCourse = null;
        if ($apply_week == $adjust_week) {
            $course->andWhere(['not', ['course.id'=>$course_id]]);

            // 同一天中未被调整的节次
            $adjustCourse = Course::findOne($course_id);
            $adjustCourse->sec = explode(',', $adjustCourse->sec);
            $apply_sec = explode(',', $apply_sec);
            $diff = array_diff($adjustCourse->sec, $apply_sec);
            if (empty($diff)) {
                $adjustCourse = null;
            } else {
                $adjustCourse->sec = implode(',', $diff);
            }
        }

        $course->andWhere('FIND_IN_SET('.$adjust_week.',week)');

        $courseCopy1 = clone $course;
        $courseCopy2 = clone $course;

        // 授课教师课程
        $teacherCourse = $course
            ->andWhere(['user_id' => $teacher_id])
            ->indexBy('id')
            ->all();
        // 班级课程
        $classes_id = ArrayHelper::getColumn(Course::findOne($course_id)->classes, 'id');
        $classCourse = $courseCopy1->innerJoinWith('classes')
            ->andWhere(['classes.id'=>$classes_id])
            ->indexBy('id')
            ->all();
        // 学生选修课程
        $userID = ArrayHelper::getColumn(Course::findOne($course_id)->students, 'id');
        $studentCourse = $courseCopy2->innerJoinWith('students')
            ->andWhere(['or', ['user.class_id'=>$classes_id], ['user.id' => $userID]])
            ->indexBy('id')
            ->all();
        // 合并课程
        $courses = $teacherCourse + $classCourse + $studentCourse + [$adjustCourse];

        // part.2 Activity表
        $classSelected = implode('|', $classes_id);
        $activities = Activity::find()
            ->andWhere('FIND_IN_SET('.$adjust_week.',week)')
            ->andWhere("CONCAT(',',`classes_ids`,',') REGEXP '[^0-9]+(".$classSelected.")[^0-9]+'")
            ->all();

        // part.3 Application表
        $application = Application::find()
        ->andWhere(['status' => Audit::STATUS_UNAUDITED])
        ->andWhere('FIND_IN_SET('.$adjust_week.',adjust_week)');

        $applicationCopy = clone $application;

        // 涉及授课教师正被申请的记录
        $teaApplication = $application->andWhere(['teacher_id' => $teacher_id])
            ->indexBy('id')
            ->all();

        // 班级课程ID
        $a = Course::find()
                ->select(['course.id'])
                ->innerJoinWith('classes')
                ->andWhere(['classes.id'=>$classes_id])
                ->column();

        // 授课班内所有学生选修课程ID
        $b = Course::find()
                ->select(['course.id'])
                ->innerJoinWith('students')
                ->andWhere(['or', ['user.class_id'=>$classes_id], ['user.id' => $userID]])
                ->column();

        // 涉及授课班内所有学生的正被申请记录
        $stuApplication = $applicationCopy
            ->andWhere(['course_id' => array_unique(array_merge($a, $b))])
            ->indexBy('id')
            ->all();

        // 合并申请记录
        $applications = $teaApplication + $stuApplication;

        return $this->renderAjax('freeTime', [
            'courses' => $courses,
            'activities' => $activities,
            'applications' => $applications,
        ]);
    }

    /**
     * 显示教师所有课程
     * @return string
     */
    public function actionAllCourses() {
        $courses = Course::find()
            ->innerJoinWith('classes')
            ->Where(['user_id' => Yii::$app->user->id])
            ->all();

        $all_courses = [];
        foreach ($courses as $course) {
            $all_courses[$course->id] = $course->name. ' @' . implode(' @', ArrayHelper::getColumn($course->classes, 'name'));
        }

        $buttons = [];
        foreach (array_unique($all_courses) as $key => $course) {
            $buttons[] = Html::button($course, [
                'data-key' => $key,
//                'style' => "margin-bottom: 4px;",
                'class' => 'btn btn-default btn-block',
            ]);
        }

        return '<div>'.implode("\n", $buttons).'</div>';
    }

    /**
     * 显示单个申请详细信息
     * @param integer $id
     * @return string
     */
    public function actionView($id)
    {
        return $this->renderAjax('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * 根据id撤销申请记录
     * @param integer $id
     * @return Response
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * 根据id找到对应申请记录
     * @param integer $id
     * @return Application
     * @throws NotFoundHttpException 如果记录不存在则跳转到404页面
     */
    protected function findModel($id)
    {
        if (($model = Application::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('所访问页面不存在!');
        }
    }

    /**
     * 根据申请内容推送给相应管理员
     * @param Application $model
     * @return bool
     */
    protected function push($model) {
        $id = $model->getAttribute('id');
        // 如果不是申请停课
        if ($model->type != Application::TYPE_SUSPEND) {
            // step.1 判断地点是否为机房或实验室
            $classroom = Classroom::findOne($model->getAttribute('classroom_id'));
            if ($classroom->type == Classroom::TYPE_SPECIAL) {
                // 推送给实验中心主任审核
                $laboratories = Adminuser::findAll(['role' => Adminuser::LABORATORY]);
                foreach ($laboratories as $laboratory) {
                    $audit = new Audit();
                    $audit->adminuser_id = $laboratory->id;
                    $audit->application_id = $id;
                    $audit->save();
                }
            }

            // step.2 推送对应班级辅导审核
            $course = Course::findOne($model->getAttribute('course_id'));
            $adminuser_id = array();
            foreach ($course->classes as $class) {
                $adminuser_id[] = $class->adminuser_id;
            }
            // 如果课程授课班级为空(即公选课等)且地点不为机房
            if (empty($adminuser_id) && $classroom->type == Classroom::TYPE_ORDINARY) {
                // 直接推送给教学副院长
                $deans = Adminuser::findAll(['role' => Adminuser::DEAN]);
                foreach ($deans as $dean) {
                    $audit = new Audit();
                    $audit->adminuser_id = $dean->id;
                    $audit->application_id = $id;
                    $audit->save();
                }
            } else {
                // 推送给辅导员
                $adminusers = array_unique($adminuser_id);
                foreach ($adminusers as $adminuser) {
                    $audit = new Audit();
                    $audit->adminuser_id = $adminuser;
                    $audit->application_id = $id;
                    $audit->save();
                }
            }
            return true;
            // 如果是申请停课,则直接推送给教学副院长审核
        } elseif ($model->type == Application::TYPE_SUSPEND) {
            $deans = Adminuser::findAll(['role' => Adminuser::DEAN]);
            foreach ($deans as $dean) {
                $audit = new Audit();
                $audit->adminuser_id = $dean->id;
                $audit->application_id = $id;
                $audit->save();
            }
            return true;
        }
        return false;
    }

}
