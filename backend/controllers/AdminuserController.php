<?php

namespace backend\controllers;

use backend\models\CreateAdminuserForm;
use backend\models\ResetpwdForm;
use Yii;
use common\models\Adminuser;
use backend\models\AdminuserSearch;
use yii\db\IntegrityException;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * 管理员管理模块控制器
 */
class AdminuserController extends Controller
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
     * 列出所有管理员
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new AdminuserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 显示单个管理员详细信息
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
     * 新增管理员
     * @return Response|string
     */
    public function actionCreate()
    {
        $model = new CreateAdminuserForm();

        // 块赋值与验证保存
        if($model->load(Yii::$app->request->post()) && $model->createAdminuser()) {
            // 操作成功提示信息
            Yii::$app->getSession()->setFlash('success', '新增管理员成功');
            return $this->redirect(['index']);
        }
        
        return $this->renderAjax('create', ['model' => $model]);
    }

    /**
     * 更新单个管理员信息
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
        $model = $id === null ? new CreateAdminuserForm() : $this->findModel($id);
        $model->load(Yii::$app->request->post());
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ActiveForm::validate($model);
    }

    /**
     * 更改管理员密码
     * @param $id
     * @return mixed
     */
    public function actionResetpwd($id)
    {
        $model = new ResetpwdForm();

        // 块赋值与重置密码
        if ($model->load(Yii::$app->request->post()) && $model->resetPassword($this->findModel($id))) {
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
     * 删除一个管理员
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        try {
            $this->findModel($id)->delete();
            // 操作成功提示信息
            Yii::$app->getSession()->setFlash('success', '删除成功');
        } catch (IntegrityException $e) {
            // 操作失败提示信息
            Yii::$app->getSession()->setFlash('error', '该管理员仍有关联!');
        }
        return $this->redirect(['index']);
    }

    /**
     * 根据id找到对应管理员记录
     * @param integer $id
     * @return Adminuser
     * @throws NotFoundHttpException 如果记录不存在则跳转到404页面
     */
    protected function findModel($id)
    {
        if (($model = Adminuser::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('所访问页面不存在!');
        }
    }
}
