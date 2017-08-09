<?php

namespace frontend\controllers;

use common\models\Adminuser;
use common\models\Application;
use common\models\Audit;
use common\models\Classroom;
use common\models\Course;
use common\widgets\ButtonsWidget;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * 停调课申请审核控制器
 */
class ApplicationController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * 列出所有申请记录
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Application::find()->where(['user_id'=>Yii::$app->user->id]),
            'pagination' => ['pageSize'=>10], //分页
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 停调课申请
     * @return mixed
     * @throws \Exception
     */
    public function actionApply()
    {
        $model = new Application();

        $model->user_id = Yii::$app->user->id;
        $model->teacher_id = Yii::$app->user->id;

        // 块赋值
        $load = $model->load(Yii::$app->request->post());

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($load && $model->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model->save(false);
                $id = $model->getAttribute('id');
                // 如果不是申请停课
                if ($model->type != Application::TYPE_SUSPEND) {
                    // 推送对应班级辅导审核
                    $course = Course::findOne($model->getAttribute('course_id'));
                    $adminuser_id = array();
                    foreach ($course->classes as $class) {
                        $adminuser_id[] = $class->adminuser_id;
                    }
                    $adminusers = array_unique($adminuser_id);
                    foreach ($adminusers as $adminuser) {
                        $audit = new Audit();
                        $audit->adminuser_id = $adminuser;
                        $audit->application_id = $id;
                        $audit->save();
                    }

                    $classroom = Classroom::findOne($model->getAttribute('classroom_id'));
                    // 判断是否是机房或实验室
                    if ($classroom->type == Classroom::TYPE_SPECIAL) {
                        // 推送给实验中心主任审核
                        $laboratories = Adminuser::findAll(['role' => Adminuser::LABORATORY]);
                        foreach ($laboratories as $laboratory) {
                            $audit = new Audit();
                            $audit->adminuser_id = $laboratory->id;
                            $audit->application_id = $id;
                            $audit->save();
                        }
                    }
                    // 如果是申请停课,则直接推送给教学副院长审核
                } elseif ($model->type == Application::TYPE_SUSPEND) {
                    $deans = Adminuser::findAll(['role' => Adminuser::DEAN]);
                    foreach ($deans as $dean) {
                        $audit = new Audit();
                        $audit->adminuser_id = $dean->id;
                        $audit->application_id = $id;
                        $audit->save();
                    }
                }
                // 提交事务
                $transaction->commit();
                Yii::$app->getSession()->setFlash('success', '申请成功, 请等待审核...');
                return $this->redirect(['index']);
            } catch (\Exception $e) {
                // 回滚
                $transaction->rollback();
                throw $e;
            }
        } else {
            return $this->render('apply', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 根据时间查询空闲教室
     * @return mixed
     */
    public function actionFreeClassroom()
    {
        $request = Yii::$app->request;
        $course_id = $request->post('course_id');
        $day = $request->post('day');
        $secSelected = is_array($request->post('sec'))?implode('|', $request->post('sec')):null;
        $weekSelected = $request->post('week');

        $query = Course::find();
        $query->andFilterWhere(['not', ['id'=>$course_id]]);
        $query->select(['classroom_id']);
        $query->andWhere(['day'=>$day]);
        $query->andWhere("CONCAT(',',`sec`,',') REGEXP '[^0-9]+(".$secSelected.")[^0-9]+'");
        $query->andWhere("CONCAT(',',`week`,',') REGEXP '[^0-9]+(".$weekSelected.")[^0-9]+'");
        $usedClassroom = $query->column();

        $freeClassroom = Classroom::find()->where(['not in', 'id', $usedClassroom])->all();

        return ButtonsWidget::widget(['classrooms'=>$freeClassroom]);

    }

    /**
     * 显示单个申请详细信息
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
     * Updates an existing Application model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Application model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * 根据id找到对应申请记录
     * 如果记录不存在则跳转到404页面
     * @param integer $id
     * @return Application the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Application::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('所访问页面不存在!');
        }
    }
}
