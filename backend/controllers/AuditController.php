<?php

namespace backend\controllers;

use common\models\Adminuser;
use common\models\Audit;
use common\models\Course;
use common\models\CourseRelationship;
use Yii;
use common\models\Application;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * 审核申请模块控制器
 */
class AuditController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        // 控制器只允许教学副院长,院办,实验中心副主任,辅导员访问
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            if (!Yii::$app->user->isGuest) {
                                $roles = [Adminuser::DEAN, Adminuser::OFFICE, Adminuser::LABORATORY, Adminuser::COUNSELOR];
                                return in_array(Yii::$app->user->identity->role, $roles,true);
                            }
                            return false;
                        },
                    ],
                ],
            ],
        ];
    }

    /**
     * 列出所有未审核申请信息
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Audit::find()
                ->where(['adminuser_id'=>Yii::$app->user->id])
                ->orderBy(['application_id' => SORT_DESC]),
            'pagination' => ['defaultPageSize' => 10],  //分页
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 显示单个申请详细信息
     * @param integer $id
     * @return string
     */
    public function actionView($id)
    {
        return $this->renderAjax('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * 审核通过
     * @param integer $id
     * @return Response
     * @throws \Exception
     */
    public function actionPass($id)
    {
        $model = $this->findModel($id);
        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // 置审核状态为通过
            $model->load(Yii::$app->request->post());
            $model->status = Audit::STATUS_PASS;
            $model->save();

            // 如果审核角色是教学副院长
            if ($model->adminuser->role == Adminuser::DEAN) {
                // 则申请状态为通过
                $application = $model->application;
                $application->status = Audit::STATUS_PASS;
                $application->save(false);
                // 更新调、停对应申请调整课程的周记录
                $course = $application->course;
                if ($application->type != Application::TYPE_SCHEDULE) {
                    $week = explode(',', $course->week);
                    $course->week = implode(',', array_diff($week, [$application->apply_week]));
                    if (empty($course->week)) {
                        $course->delete();
                    } else {
                        $course->save(false);
                    }
                }
                // 新增调、排课记录
                if ($application->type != Application::TYPE_SUSPEND) {
                    // 新增课程
                    $newCourse = new Course();
                    $newCourse->number = $course->number;
                    $newCourse->name = $course->name;
                    $newCourse->user_id = $application->teacher_id;
                    $newCourse->day = $application->adjust_day;

                    // 如果是调课 , 还要处理sec, 加上同一天中未被申请调整的节次
                    if ($application->type == Application::TYPE_ADJUST) {
                        $course->sec = explode(',', $course->sec);
                        $application->apply_sec = explode(',', $application->apply_sec);
                        $application->adjust_sec = explode(',', $application->adjust_sec);
                        $diff = array_diff($course->sec, $application->apply_sec);
                        $newCourse->sec = array_merge($diff, $application->adjust_sec);
                        $a = $newCourse->sec;
                        sort($a);
                        $newCourse->sec = $a;
                    } else {
                        $newCourse->sec = $application->adjust_sec;
                    }

                    $newCourse->week = $application->adjust_week;
                    $newCourse->classroom_id = $application->classroom->name;

                    if ($newCourse->save()) {
                        // 新增班级关联
                        foreach ($course->classes as $class) {
                            $newRelation = new CourseRelationship();
                            $newRelation->class_id = $class->id;
                            $newRelation->course_id = $newCourse->getAttribute('id');
                            $newRelation->save();
                        }
                    } else {
                        throw new \Exception('保存失败~');
                    }
                }
            }

            // 如果审核角色不是教学副院长
            if ($model->adminuser->role != Adminuser::DEAN) {
                // 判断是否将申请推送至教学副院长
                $audits = Audit::findAll(['application_id'=>$model->application_id]);
                $push = false;
                foreach ($audits as $audit) {
                    if ($audit->status == Audit::STATUS_PASS) {
                        $push = true;
                    } else {
                        $push = false;
                        break;
                    }
                }
                // 如果其他角色审核都通过则推送至教学副院长做最终审核
                if ($push) {
                    $deans = Adminuser::findAll(['role' => Adminuser::DEAN]);
                    foreach ($deans as $dean) {
                        $audit = new Audit();
                        $audit->adminuser_id = $dean->id;
                        $audit->application_id = $model->application_id;
                        $audit->save();
                    }
                }
            }

            // 提交事务
            $transaction->commit();
            // 发送审核成功提示信息
            Yii::$app->getSession()->setFlash('success', '审核已成功提交...');
        } catch (\Exception $e) {
            // 发送审核失败提示信息
            Yii::$app->getSession()->setFlash('error', '审核失败, 请重新审核...');
            // 回滚
            $transaction->rollback();
            throw $e;
        }

        return $this->redirect(['index']);
    }

    /**
     * 审核不通过
     * @param integer $id
     * @return Response
     * @throws \Exception
     */
    public function actionFailed($id)
    {
        $model = $this->findModel($id);
        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // 置审核状态为不通过
            $model->load(Yii::$app->request->post());
            $model->status = Audit::STATUS_FAILED;
            $model->save();

            // 置申请状态为不通过
            $application = $model->application;
            $application->status = Audit::STATUS_FAILED;
            $application->save(false);

            // 删除其他角色未做审核的审核记录
            Audit::deleteAll(['application_id'=>$application->id, 'status'=>Audit::STATUS_UNAUDITED]);

            // 提交事务
            $transaction->commit();
            // 发送审核成功提示信息
            Yii::$app->getSession()->setFlash('success', '审核已提交...');
        } catch (\Exception $e) {
            // 发送审核失败提示信息
            Yii::$app->getSession()->setFlash('error', '审核失败, 请重新审核...');
            // 回滚
            $transaction->rollback();
            throw $e;
        }

        return $this->redirect(['index']);
    }

    /**
     * 根据id找到对应申请记录
     * @param integer $id
     * @return Audit
     * @throws NotFoundHttpException 如果记录不存在则跳转到404页面
     */
    protected function findModel($id)
    {
        if (($model = Audit::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('所访问页面不存在!');
        }
    }
}
