<?php

namespace backend\controllers;

use backend\models\CreateAdminuserForm;
use backend\models\ResetpwdForm;
use Yii;
use common\models\Adminuser;
use backend\models\AdminuserSearch;
use yii\db\IntegrityException;
use yii\filters\AccessControl;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii\web\Response;
use yii\widgets\ActiveForm;

//use yii\widgets\ActiveForm;

/**
 * Adminuser模型控制器(实现增删查改动作)
 */
class AdminuserController extends Controller
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
     * 列出所有管理员
     * @return mixed
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
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * 新增管理员
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CreateAdminuserForm();
        // 块赋值验证
        $load = $model->load(Yii::$app->request->post());

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if($load && $model->createAdminuser()) {
            return $this->redirect(['index']);
        }
        
        return $this->render('create', ['model' => $model]);
    }

    /**
     * 更新单个管理员信息
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        // 块赋值验证
        $load = $model->load(Yii::$app->request->post());

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($load && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', ['model' => $model]);
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
            return $this->render('resetpwd', ['model' => $model]);
        }
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
        } catch (IntegrityException $e) {
            Yii::$app->getSession()->setFlash('error', '该管理员仍有关联!');
        }
        return $this->redirect(['index']);
    }

    /**
     * 根据id找到对应管理员记录
     * 如果记录不存在则跳转到404页面
     * @param integer $id
     * @return Adminuser the loaded model
     * @throws NotFoundHttpException if the model cannot be found
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
