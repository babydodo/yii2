<?php

namespace backend\controllers;

use common\models\Adminuser;
use common\models\Course;
use Yii;
use common\models\Classes;
use backend\models\ClassesSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * 班级管理模块控制器
 */
class ClassesController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        // 控制器只允许系主任角色访问
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
     * @return string
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * 新增一个班级
     * @return Response|string
     */
    public function actionCreate()
    {
        $model = new Classes();

        // 块赋值与验证保存
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', '新增班级成功');
            return $this->redirect(['index']);
        } else {
            return $this->renderAjax('create', ['model' => $model]);
        }
    }

    /**
     * 更新班级信息
     * @param integer $id
     * @return Response|string
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        // 块赋值与验证保存
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', '修改资料成功');
            return $this->redirect(['index']);
        } else {
            return $this->renderAjax('update', ['model' => $model]);
        }
    }

    /**
     * 验证新增与修改表单
     * @param null|integer $id
     * @return array
     */
    public function actionValidateSave($id = null)
    {
        $model = $id === null ? new Classes() : $this->findModel($id);
        $model->load(Yii::$app->request->post());
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ActiveForm::validate($model);
    }

    /**
     * 显示班级详细课表
     * @param integer $id
     * @param integer $week
     * @return string
     */
    public function actionShowCourses($id, $week=1)
    {
        // 班级课程表
        $model = Course::find()
            ->innerJoinWith('classes')
            ->where(['classes.id'=>$id])
            ->andWhere('FIND_IN_SET('.$week.',week)')
            ->all();

        return $this->renderAjax('showCourses', ['courses' => $model]);
    }

    /**
     * 删除一个班级
     * @param integer $id
     * @return Response
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * 根据id找到对应班级记录
     * @param integer $id
     * @return Classes
     * @throws NotFoundHttpException 如果记录不存在则跳转到404页面
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
