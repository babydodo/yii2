<?php

namespace backend\controllers;

use common\models\Adminuser;
use Yii;
use common\models\Classroom;
use backend\models\ClassroomSearch;
use yii\db\IntegrityException;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * 教室管理模块控制器
 */
class ClassroomController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        // 控制器允许院长,教学副院长,院办,系主任访问
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            if (!Yii::$app->user->isGuest) {
                                $roles = [Adminuser::BOSS, Adminuser::DEAN, Adminuser::OFFICE,Adminuser::DIRECTOR];
                                return in_array(Yii::$app->user->identity->role, $roles,true);
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
     * 列出所有教室信息
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ClassroomSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 显示单个教室详细信息
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
     * 新增一个教室
     * @return Response|string
     */
    public function actionCreate()
    {
        $model = new Classroom();

        // 块赋值与验证保存
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // 操作成功提示信息
            Yii::$app->getSession()->setFlash('success', '新增教室成功');
            return $this->redirect(['index']);
        } else {
            return $this->renderAjax('create', ['model' => $model]);
        }
    }

    /**
     * 修改教室信息
     * @param integer $id
     * @return Response|string
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        // 块赋值与验证保存
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // 操作成功提示信息
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
        $model = $id === null ? new Classroom() : $this->findModel($id);
        $model->load(Yii::$app->request->post());
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ActiveForm::validate($model);
    }

    /**
     * 删除一个教室
     * @param integer $id
     * @return Response
     */
    public function actionDelete($id)
    {
        try {
            $this->findModel($id)->delete();
            // 操作成功提示信息
            Yii::$app->getSession()->setFlash('success', '删除成功');
        } catch (IntegrityException $e) {
            // 操作失败提示信息
            Yii::$app->getSession()->setFlash('error', '该教室仍有关联!');
        }
        return $this->redirect(['index']);
    }

    /**
     * 根据id找到对应教室记录
     * @param integer $id
     * @return Classroom
     * @throws NotFoundHttpException 如果记录不存在则跳转到404页面
     */
    protected function findModel($id)
    {
        if (($model = Classroom::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('所访问页面不存在!');
        }
    }
}
