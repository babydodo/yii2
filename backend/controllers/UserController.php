<?php

namespace backend\controllers;

use backend\models\CreateUserForm;
use backend\models\ResetpwdForm;
use Yii;
use common\models\User;
use backend\models\UserSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;

/**
 * User模型控制器(实现增删查改动作)
 */
class UserController extends Controller
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
                                return Yii::$app->user->identity->role == 1 ? true : false;
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
     */
    public function actionCreate()
    {
        $model = new CreateUserForm();

        if (Yii::$app->request->isAjax) {
            // 块赋值验证
            $model->load($_POST);
            Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
            return  ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->createUser()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 更新一个用户的信息
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
     * 更新用户密码
     */
    public function actionResetpwd($id)
    {
        $model = new ResetpwdForm();

        if ($model->load(Yii::$app->request->post()) && $model->resetPassword($id)) {
            return $this->redirect(['index']);
        } else {
            return $this->render('resetpwd', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 删除一个用户
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * 根据id找到对应用户记录
     * 如果记录不存在则跳转到404页面
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
