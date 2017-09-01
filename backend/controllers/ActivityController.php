<?php

namespace backend\controllers;

use backend\models\CourseForm;
use common\models\Adminuser;
use common\models\Classroom;
use common\widgets\ButtonsWidget;
use Yii;
use common\models\Course;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * 课外活动管理控制器
 */
class ActivityController extends Controller
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
                                return Yii::$app->user->identity->role == Adminuser::COUNSELOR ? true : false;
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
     * 列出辅导员安排的所有课外活动信息
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Course::find()
                ->where(['number'=>Yii::$app->user->id])
                ->orderBy(['id' => SORT_DESC]),
            'pagination' => ['pageSize'=>10],   //分页
        ]);
        $dataProvider->setSort(false);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 新增课外活动
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CourseForm();

        // 块赋值验证
        $load = $model->load(Yii::$app->request->post());
        $model->number = Yii::$app->user->id;
        $model->user_id = Yii::$app->user->id;

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
     * 更新课外活动信息
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
     * 删除课外活动信息
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * 根据时间查询空闲教室
     * @return mixed
     */
    public function actionFreeClassroom()
    {
        $request = Yii::$app->request;
        $id = $request->post('id');
        $day = $request->post('day');
        $secSelected = is_array($request->post('sec'))?implode('|', $request->post('sec')):null;
        $weekSelected = is_array($request->post('week'))?implode('|', $request->post('week')):null;

        if (empty($day) || empty($secSelected) || empty($weekSelected)) {
            return '请先选择 时间段';
        }

        $query = Course::find();
        $query->andFilterWhere(['not', ['id'=>$id]]);
        $query->select(['classroom_id']);
        $query->andWhere(['day'=>$day]);
        $query->andWhere("CONCAT(',',`sec`,',') REGEXP '[^0-9]+(".$secSelected.")[^0-9]+'");
        $query->andWhere("CONCAT(',',`week`,',') REGEXP '[^0-9]+(".$weekSelected.")[^0-9]+'");
        $usedClassroom = $query->column();

        $freeClassroom = Classroom::find()->where(['not in', 'id', $usedClassroom])->all();

        return ButtonsWidget::widget(['classrooms'=>$freeClassroom]);
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
