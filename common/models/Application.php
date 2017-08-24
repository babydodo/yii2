<?php

namespace common\models;
use Yii;
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
            [['apply_week', 'apply_sec', 'adjust_week', 'adjust_sec'], 'string', 'max' => 64],
            [['reason'], 'string', 'max' => 255],
            [['course_id'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['course_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['classroom_id'], 'exist', 'skipOnError' => true, 'targetClass' => Classroom::className(), 'targetAttribute' => ['classroom_id' => 'id']],
            [['teacher_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['teacher_id' => 'id']],
            [['apply_week', 'apply_sec', 'adjust_week', 'adjust_day', 'adjust_sec', 'classroom_id', 'teacher_id'], 'default', 'value' => null],

        ];
    }

    /**
     * 验证教室在所选时间段是否空闲(验证规则)
     * @param string $attribute
     * @param array $params
     */
    public function validateClassroom($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $secSelected = str_replace(',', '|', $this->adjust_sec);
            $weekSelected = $this->adjust_week;

            $query = Course::find();
            $query->andFilterWhere(['not', ['id'=>$this->course_id]]);
            $query->andWhere(['day'=>$this->adjust_day]);
            $query->andWhere("CONCAT(',',`sec`,',') REGEXP '[^0-9]+(".$secSelected.")[^0-9]+'");
            $query->andWhere("CONCAT(',',`week`,',') REGEXP '[^0-9]+(".$weekSelected.")[^0-9]+'");

            if ($query->andWhere(['classroom_id'=>$this->classroom_id])->one()) {
                $this->addError($attribute, '教室已被占用');
            }
        }
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
    public function validateTime($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $secSelected = str_replace(',', '|', $this->adjust_sec);
            $weekSelected = $this->adjust_week;

            // 筛选选定时间段所有信息
            $query = Course::find();
            $query->andWhere(['not', ['course.id'=>$this->course_id]]);
            $query->andWhere(['day' => $this->adjust_day]);
            $query->andWhere("CONCAT(',',`sec`,',') REGEXP '[^0-9]+(" . $secSelected . ")[^0-9]+'");
            $query->andWhere('FIND_IN_SET('.$weekSelected.',week)');

            $queryCopy1 = clone $query;
            $queryCopy2 = clone $query;

            // 验证教师是否空闲
            if ($query->andWhere(['user_id' => $this->teacher_id])->one()) {
                $this->addError($attribute, '该教师在此时间段有课');
            } else {
                // 验证班级是否空闲
                $classID = ArrayHelper::getColumn(Course::findOne($this->course_id)->classes, 'id');
                if ($queryCopy1->innerJoinWith('classes')->andWhere(['classes.id'=>$classID])->one()) {
                    $this->addError($attribute, '所选班级在此时间段有课');
                    // 验证对应班级所有学生是否空闲
                } elseif ($queryCopy2->innerJoinWith('students')->andWhere(['user.class_id'=>$classID])->one()) {
                    $this->addError($attribute, '所选班级有学生在此时间段有课');
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
            'adjust_sec' => '调整后节次',
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
            Audit::STATUS_FAILED=>'不通过',
            Audit::STATUS_PASS=>'通过',
        ];
        return $statusStr[$this->status];
    }

}
