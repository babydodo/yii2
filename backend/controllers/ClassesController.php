<?php

namespace backend\controllers;

use common\models\Adminuser;
use common\models\Course;
use Yii;
use common\models\Classes;
use backend\models\ClassesSearch;
use yii\filters\AccessControl;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;

/**
 * 班级管理控制器
 */
class ClassesController extends Controller
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
     * 列出所有班级信息
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ClassesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 显示单个班级详细信息
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * 新增一个班级
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Classes();

        if (Yii::$app->request->isAjax) {
            // 块赋值验证
            $model->load($_POST);
            Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
            return  ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 更新班级信息
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isAjax) {
            // 块赋值验证
            $model->load($_POST);
            Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
            return  ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 显示班级详细课表
     * @param integer $id
     * @return mixed
     */
    public function actionShowCourses($id)
    {
        // 班级课程表
        $model = Course::find()->innerJoinWith('classes');
        $model->where(['classes.id'=>$id]);
        $model->andWhere('FIND_IN_SET(3,week)');
        $courses = $model->all();

        return $this->renderAjax('showCourses', ['courses' => $courses]);
    }

    /**
     * 删除一个班级
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }



    /**
     * 根据id找到对应班级记录
     * 如果记录不存在则跳转到404页面
     * @param integer $id
     * @return Classes the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Classes::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('所访问页面不存在!');
        }
    }
}
