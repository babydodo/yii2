<?php

namespace backend\controllers;

use backend\models\ActivitySearch;
use common\models\Activity;
use common\models\Adminuser;
use common\models\Application;
use common\models\Audit;
use common\models\Classroom;
use common\widgets\ButtonsWidget;
use Yii;
use common\models\Course;
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
        // 控制器只允许辅导员角色访问
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
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ActivitySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 新增课外活动
     * @return array|Response|string
     */
    public function actionCreate()
    {
        $model = new Activity();

        // 块赋值
        $load = $model->load(Yii::$app->request->post());
        $model->adminuser_id = Yii::$app->user->id;

        // Ajax验证
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        // 保存
        if ($load && $model->save()) {
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
     * @return array|Response|string
     */
    public function actionUpdate($id)
    {
        $model = Activity::findOne($id);
        $model->formatAttributes();
        $model->classroom_id = Classroom::findOne($model->classroom_id)->getAttribute('name');
        $model->classes_ids = explode(',', $model->classes_ids);

        // 块赋值
        $load = $model->load(Yii::$app->request->post());

        // Ajax验证
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        // 保存
        if ($load && $model->save($id)) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 根据id删除课外活动信息
     * @param integer $id
     * @return Response
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * 根据时间段查询空闲教室
     * @return string|ButtonsWidget
     */
    public function actionFreeClassroom()
    {
        $request = Yii::$app->request;
        $id = $request->post('id');
        $day = $request->post('day');
        $secSelected = is_array($request->post('sec'))?implode('|', $request->post('sec')):$request->post('sec');
        $weekSelected = is_array($request->post('week'))?implode('|', $request->post('week')):$request->post('week');

        if (empty($day) || empty($secSelected) || empty($weekSelected)) {
            return '请先选择 时间段';
        }

        // Course表中某一时间段使用的教室
        $usedClassroom1 = Course::find()
            ->select(['classroom_id'])
            ->andWhere(['day'=>$day])
            ->andWhere("CONCAT(',',`sec`,',') REGEXP '[^0-9]+(".$secSelected.")[^0-9]+'")
            ->andWhere("CONCAT(',',`week`,',') REGEXP '[^0-9]+(".$weekSelected.")[^0-9]+'")
            ->column();

        // Application表中某一时间段使用的教室
        $usedClassroom2 = Application::find()
            ->select(['classroom_id'])
            ->andWhere(['status' => Audit::STATUS_UNAUDITED])
            ->andWhere("CONCAT(',',`adjust_week`,',') REGEXP '[^0-9]+(".$weekSelected.")[^0-9]+'")
            ->andWhere(['adjust_day'=>$day])
            ->andWhere("CONCAT(',',`adjust_sec`,',') REGEXP '[^0-9]+(".$secSelected.")[^0-9]+'")
            ->column();

        // Activity表中某一时间段使用的教室
        $usedClassroom3 = Activity::find()
            ->select(['classroom_id'])
            ->andFilterWhere(['not', ['id'=>$id]])
            ->andWhere("CONCAT(',',`week`,',') REGEXP '[^0-9]+(".$weekSelected.")[^0-9]+'")
            ->andWhere(['day'=>$day])
            ->andWhere("CONCAT(',',`sec`,',') REGEXP '[^0-9]+(".$secSelected.")[^0-9]+'")
            ->column();

        // 合并为某一时间段中使用或者被申请的教室
        $usedClassroom = array_merge($usedClassroom1, $usedClassroom2, $usedClassroom3);

        $freeClassroom = Classroom::find()->where(['not in', 'id', $usedClassroom])->all();

        return ButtonsWidget::widget(['classrooms'=>$freeClassroom]);
    }

    /**
     * 根据id找到对应课程记录
     * @param integer $id
     * @return Course
     * @throws NotFoundHttpException 如果记录不存在则跳转到404页面
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
