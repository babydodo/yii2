<?php

namespace common\models;
use yii\helpers\ArrayHelper;

/**
 * application表模型类
 *
 * @property integer $id
 * @property integer $course_id
 * @property integer $user_id
 * @property integer $apply_at
 * @property string $apply_week
 * @property string $apply_sec
 * @property string $adjust_week
 * @property integer $adjust_day
 * @property string $adjust_sec
 * @property integer $classroom_id
 * @property integer $teacher_id
 * @property integer $type
 * @property string $reason
 * @property integer $status
 *
 * @property Course $course
 * @property User $user
 * @property Classroom $classroom
 * @property User $teacher
 * @property Audit[] $audits
 */
class Application extends \yii\db\ActiveRecord
{
    const TYPE_ADJUST = 1;
    const TYPE_SUSPEND = 2;
    const TYPE_SCHEDULE = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'application';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['reason', 'trim'],
            [['course_id', 'user_id', 'type', 'reason'], 'required'],
            ['apply_week', 'required', 'when' => function ($model) {
                return $model->type != self::TYPE_SCHEDULE;
            }, 'whenClient' => "function (attribute, value) {
                return $('#application-type').val() != 3;
            }"],
            ['adjust_week', 'required', 'when' => function ($model) {
                return $model->type != self::TYPE_SUSPEND;
            }, 'whenClient' => "function (attribute, value) {
                return $('#application-type').val() != 2;
            }"],
            ['adjust_sec', 'required', 'when' => function ($model) {
                return $model->type != self::TYPE_SUSPEND;
            }, 'whenClient' => "function (attribute, value) {
                return $('#application-type').val() != 2;
            }"],
            ['classroom_id', 'required', 'when' => function ($model) {
                return $model->type != self::TYPE_SUSPEND;
            }, 'whenClient' => "function (attribute, value) {
                return $('#application-type').val() != 2;
            }"],
            ['classroom_id', 'filter', 'filter' => function ($value) {
                return empty($value)?$value:Classroom::find()->where(['name'=>$value])->scalar();
            }, 'skipOnArray' => true],
            ['status', 'default', 'value' => Audit::STATUS_UNAUDITED],
            [['course_id', 'user_id', 'apply_at', 'adjust_day', 'teacher_id', 'type', 'status'], 'integer'],
            ['adjust_sec', 'filter', 'filter' => function ($value) {
                return is_array($value)?implode(',', $value):$value;
            }],
            ['adjust_day', 'in', 'range' => [1, 2, 3, 4, 5, 6, 7] ],
            ['type', 'in', 'range' => [self::TYPE_ADJUST, self::TYPE_SUSPEND, self::TYPE_SCHEDULE] ],
            ['status', 'in', 'range' => [Audit::STATUS_FAILED, Audit::STATUS_UNAUDITED, Audit::STATUS_PASS] ],
            [['apply_week', 'apply_sec', 'adjust_week', 'adjust_sec'], 'string', 'max' => 64],
            [['reason'], 'string', 'max' => 255],
            [['course_id'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['course_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['classroom_id'], 'exist', 'skipOnError' => true, 'targetClass' => Classroom::className(), 'targetAttribute' => ['classroom_id' => 'id']],
            [['teacher_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['teacher_id' => 'id']],
            [['apply_week', 'apply_sec', 'adjust_week', 'adjust_day', 'adjust_sec', 'classroom_id', 'teacher_id'], 'default', 'value' => null],
            // 自定义验证规则
            ['classroom_id', 'validateClassroom'],
            ['adjust_sec', 'validateFreeTime'],
        ];
    }

    /**
     * 验证申请周与课程是否对应一致(验证规则)
     * @param string $attribute
     * @param array $params
     */
    public function validateCourse($attribute, $params)
    {
    }

    /**
     * 验证调整后时间段是否可用(验证规则)
     * @param string $attribute
     * @param array $params
     */
    public function validateFreeTime($attribute, $params)
    {
        if (!$this->hasErrors()) {
            // 如果是停课, 则无需验证
            if ($this->type == self::TYPE_SUSPEND) {
                return;
            }

            $secSelected = str_replace(',', '|', $this->adjust_sec);
            $classID = ArrayHelper::getColumn(Course::findOne($this->course_id)->classes, 'id');
            $userID = ArrayHelper::getColumn(Course::findOne($this->course_id)->students, 'id');

            // part.1 验证Course表
            $course = Course::find()
                ->andWhere(['not', ['course.id'=>$this->course_id]])
                ->andWhere('FIND_IN_SET('.$this->adjust_week.',week)')
                ->andWhere(['day' => $this->adjust_day])
                ->andWhere("CONCAT(',',`sec`,',') REGEXP '[^0-9]+(" . $secSelected . ")[^0-9]+'");

            $courseCopy1 = clone $course;
            $courseCopy2 = clone $course;

            // 验证教师是否空闲
            if ($course->andWhere(['user_id' => $this->teacher_id])->one()) {
                $this->addError($attribute, '该教师在此时间段有课');
                // 验证班级是否空闲
            } elseif ($courseCopy1->innerJoinWith('classes')->andWhere(['classes.id'=>$classID])->one()) {
                    $this->addError($attribute, '班级在此时间段有课');
            } else {
                // 验证对应班级所有学生是否空闲
                if ($courseCopy2->innerJoinWith('students')->andWhere(['or', ['user.class_id'=>$classID], ['user.id' => $userID]])->one()) {
                    $this->addError($attribute, '班级有学生在此时间段有课');
                }
            }

            // part.2 验证Application表
            $application = self::find()
                ->andWhere(['status' => Audit::STATUS_UNAUDITED])
                ->andWhere('FIND_IN_SET('.$this->adjust_week.',adjust_week)')
                ->andWhere(['adjust_day'=>$this->adjust_day])
                ->andWhere("CONCAT(',',`adjust_sec`,',') REGEXP '[^0-9]+(".$secSelected.")[^0-9]+'");

            $applicationCopy = clone $application;
            $course_id = $applicationCopy->select('course_id')->column();

            // 判断教师是否被申请排课
            if ($application->andWhere(['teacher_id' => $this->teacher_id])->one()) {
                $this->addError($attribute, '授课教师在此时间段正在被申请排课');
            } elseif (!empty($course_id)){
                // 判断班级是否正被申请排课
                if (Course::find()
                        ->innerJoinWith('classes')
                        ->andWhere(['classes.id'=>$classID, 'course.id'=>$course_id])
                        ->one()
                ) {
                    $this->addError($attribute, '班级在此时间段正在被申请排课');
                    // 判断班级是否有学生正被申请排课
                } elseif (Course::find()
                            ->innerJoinWith('students')
                            ->andWhere(['course.id'=>$course_id])
                            ->andWhere(['or', ['user.class_id'=>$classID], ['user.id' => $userID]])
                            ->one()
                ) {
                    $this->addError($attribute, '班级内有学生在此时间段正在被申请排课');
                }
            }

            // part.3 验证Activity表
            $activity = Activity::find()
                ->andWhere('FIND_IN_SET('.$this->adjust_week.',week)')
                ->andWhere(['day'=>$this->adjust_day])
                ->andWhere("CONCAT(',',`sec`,',') REGEXP '[^0-9]+(".$secSelected.")[^0-9]+'");
            // 判断班级是否有活动安排
            $classSelected = implode('|', $classID);
            if($activity->andWhere("CONCAT(',',`classes_ids`,',') REGEXP '[^0-9]+(".$classSelected.")[^0-9]+'")->one()) {
                $this->addError($attribute, '所选班级在此时间段已有活动安排');
            }
        }
    }

    /**
     * 验证教室在所选时间段是否空闲(验证规则)
     * @param string $attribute
     * @param array $params
     */
    public function validateClassroom($attribute, $params)
    {
        if (!$this->hasErrors()) {
            // 如果是停课, 则无需验证
            if ($this->type == self::TYPE_SUSPEND) {
                return;
            }

            $secSelected = str_replace(',', '|', $this->adjust_sec);

            // part.1 验证Course表
            $course = Course::find()
                ->andWhere(['not', ['id'=>$this->course_id]])
                ->andWhere('FIND_IN_SET('.$this->adjust_week.',week)')
                ->andWhere(['day'=>$this->adjust_day])
                ->andWhere("CONCAT(',',`sec`,',') REGEXP '[^0-9]+(".$secSelected.")[^0-9]+'");
            if ($course->andWhere(['classroom_id'=>$this->classroom_id])->one()) {
                $this->addError($attribute, '教室已被占用');
            } else {

                // part.2 验证Application表
                $application = self::find()
                    ->andWhere(['status' => Audit::STATUS_UNAUDITED])
                    ->andWhere('FIND_IN_SET('.$this->adjust_week.',adjust_week)')
                    ->andWhere(['adjust_day'=>$this->adjust_day])
                    ->andWhere("CONCAT(',',`adjust_sec`,',') REGEXP '[^0-9]+(".$secSelected.")[^0-9]+'");
                if ($application->andWhere(['classroom_id'=>$this->classroom_id])->one()) {
                    $this->addError($attribute, '教室正在被申请使用');
                } else {

                    // part.3 验证Activity表
                    $activity = Activity::find()
                        ->andWhere('FIND_IN_SET('.$this->adjust_week.',week)')
                        ->andWhere(['day'=>$this->adjust_day])
                        ->andWhere("CONCAT(',',`sec`,',') REGEXP '[^0-9]+(".$secSelected.")[^0-9]+'");
                    if ($activity->andWhere(['classroom_id'=>$this->classroom_id])->one()) {
                        $this->addError($attribute, '教室已被占用');
                    }
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'course_id' => '调整课程',
            'user_id' => '申请人',
            'apply_at' => '申请时间',
            'apply_week' => '需调整周次',
            'apply_sec' => '调整前节次',
            'adjust_week' => '调整后周次',
            'adjust_day' => '调整后星期',
            'adjust_sec' => '调整后时间段',
            'classroom_id' => '调整后教室',
            'teacher_id' => '调整后授课教师',
            'type' => '类型',
            'reason' => '事由',
            'status' => '状态',
            'typeStr' => '类型',
            'statusStr' => '状态',
        ];
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->apply_at = time();
                // 停课
                if ($this->type == self::TYPE_SUSPEND) {
                    $this->teacher_id = null;
                    $this->adjust_week = null;
                    $this->adjust_day = null;
                    $this->adjust_sec = null;
                    $this->classroom_id = null;
                // 排课
                } elseif ($this->type == self::TYPE_SCHEDULE) {
                    $this->apply_week = null;
                    $this->apply_sec = null;
                }
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourse()
    {
        return $this->hasOne(Course::className(), ['id' => 'course_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClassroom()
    {
        return $this->hasOne(Classroom::className(), ['id' => 'classroom_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeacher()
    {
        return $this->hasOne(User::className(), ['id' => 'teacher_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAudits()
    {
        return $this->hasMany(Audit::className(), ['application_id' => 'id']);
    }

    /**
     * @return array
     */
    public static function allTypes()
    {
        return [
            self::TYPE_ADJUST =>'调课',
            self::TYPE_SUSPEND =>'停课',
            self::TYPE_SCHEDULE =>'排课',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTypeStr()
    {
        $typeStr = [
            self::TYPE_ADJUST=>'调课',
            self::TYPE_SUSPEND=>'停课',
            self::TYPE_SCHEDULE=>'排课',
        ];
        return $typeStr[$this->type];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatusStr()
    {
        $statusStr = [
            Audit::STATUS_UNAUDITED=>'待审核',
            Audit::STATUS_FAILED=>'未通过',
            Audit::STATUS_PASS=>'已通过',
        ];
        return $statusStr[$this->status];
    }

}
