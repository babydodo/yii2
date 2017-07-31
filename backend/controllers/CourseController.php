<?php

namespace backend\controllers;

use backend\models\CourseForm;
use common\models\Adminuser;
use common\models\Classes;
use common\models\Classroom;
use common\models\User;
use Yii;
use common\models\Course;
use backend\models\CourseSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * 课程管理控制器
 */
class CourseController extends Controller
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
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * 列出所有课程信息
     * @return mixed
     */
    public function actionIndex()
    {
        // 教师课程表
//        $model = Course::find()->where(['user_id'=>5])->andWhere('FIND_IN_SET(3,week)')->all();

        // 班级课程表
//        $model = Course::find()->innerJoinWith('classes');
//        $model->where(['classes.id'=>'2']);
//        $model->andWhere('FIND_IN_SET(3,week)');
//        $courses = $model->all();

        // 学生课表
//        $model = Course::find()->JoinWith(['classes', 'students']);
//        $model->where(['user.id'=>'3']);
//        $model->orWhere(['classes.id'=>'2']);
//        $model->andWhere('FIND_IN_SET(3,week)');
//        $courses = $model->all();

//        foreach ($courses as $k=>$co) {
//            $se = explode(',', $co['sec']);
//            $courses[$k]['sec'] = $se[0];
//            $courses[$k]['count'] = count($se);
//            unset($courses[$k]['students']);
//            unset($courses[$k]['classes']);
//        }

//        $courses = ArrayHelper::index($courses,function ($element) {
//            return $element['day'];
//        },'sec');

//        $data = ArrayHelper::toArray($courses, [
//            'common\models\Course' => [
//                'day'=>['id'],
//            ],
//        ]);

//        VarDumper::dump($courses);die();

        $searchModel = new CourseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 新增课程
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CourseForm();

        // 块赋值验证
        $load = $model->load(Yii::$app->request->post());

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($load && $model->saveCourse()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 更新课程信息
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = CourseForm::findOne($id);
        $model->formatAttributes();
        $model->classroom_id = Classroom::findOne($model->classroom_id)->getAttribute('name');
        $model->classID = $model->getClasses()->column();

        // 块赋值验证
        $load = $model->load(Yii::$app->request->post());

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($load && $model->saveCourse($id)) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 删除课程信息
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * 新增课程
     * @return mixed
     */
    public function actionFreeClassroom()
    {
        $request = Yii::$app->request;
        $day = $request->post('day');
        $secSelected = implode('|', $request->post('sec'));
        $weekSelected = implode('|', $request->post('week'));

        $query = Course::find();
//        $query->andFilterWhere(['not', ['course.id'=>$id]]);
        $query->select(['classroom_id']);
        $query->andWhere(['day'=>$day]);
        $query->andWhere("CONCAT(',',`sec`,',') REGEXP '[^0-9]+(".$secSelected.")[^0-9]+'");
        $query->andWhere("CONCAT(',',`week`,',') REGEXP '[^0-9]+(".$weekSelected.")[^0-9]+'");
        $usedClassroom = $query->column();

        $freeClassroom = Classroom::find()->select(['name', 'id'])->where(['not in', 'id', $usedClassroom])->indexBy('id')->column();



        return implode(',', $freeClassroom);
    }

    /**
     * 根据id找到对应课程记录
     * 如果记录不存在则跳转到404页面
     * @param integer $id
     * @return Course the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Course::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('所访问页面不存在!');
        }
    }
}
