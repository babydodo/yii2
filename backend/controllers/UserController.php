<?php

namespace backend\controllers;

use backend\models\CreateUserForm;
use backend\models\ResetpwdForm;
use common\models\Adminuser;
use Yii;
use common\models\User;
use backend\models\UserSearch;
use yii\db\IntegrityException;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * 用户管理模块控制器
 */
class UserController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        // 控制器允许院长,教学副院长,院办,系主任,辅导员访问
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            if (!Yii::$app->user->isGuest) {
                                $roles = [
                                    Adminuser::BOSS,
                                    Adminuser::DEAN,
                                    Adminuser::OFFICE,
                                    Adminuser::DIRECTOR,
                                    Adminuser::COUNSELOR,
                                ];
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
     * 列出所有用户信息
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 新增一个用户
     * @return Response|string
     */
    public function actionCreate()
    {
        $model = new CreateUserForm();

        // 块赋值与验证保存
        if ($model->load(Yii::$app->request->post()) && $model->createUser()) {
            // 操作成功提示信息
            Yii::$app->getSession()->setFlash('success', '新增用户成功');
            return $this->redirect(['index']);
        } else {
            return $this->renderAjax('create', ['model' => $model]);
        }
    }

    /**
     * 更新一个用户的信息
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
        $model = $id === null ? new CreateUserForm() : $this->findModel($id);
        $model->load(Yii::$app->request->post());
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ActiveForm::validate($model);
    }

    /**
     * 更新用户密码
     * @param integer $id
     * @return boolean|string
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
     * 删除一个用户
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
            Yii::$app->getSession()->setFlash('error', '该用户仍有关联!');
        }
        return $this->redirect(['index']);
    }

    /**
     * 根据id找到对应用户记录
     * @param integer $id
     * @return User
     * @throws NotFoundHttpException 如果记录不存在则跳转到404页面
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('所访问页面不存在!');
        }
    }
}
