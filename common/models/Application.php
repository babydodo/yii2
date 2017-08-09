<?php

namespace common\models;

/**
 * application表模型类
 *
 * @property integer $id
 * @property integer $course_id
 * @property integer $user_id
 * @property integer $apply_at
 * @property string $apply_week
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
            ['apply_week', 'required',
                'when' => function ($model) {
                    return $model->type != self::TYPE_SCHEDULE;
                },
                'whenClient' => "function (attribute, value) {
                    return $('#type').value != 3;
                }"
            ],
            [['adjust_week', 'adjust_day', 'adjust_sec', 'classroom_id', 'teacher_id'], 'required',
                'when' => function ($model) {
                    return $model->type != self::TYPE_SUSPEND;
                },
                'whenClient' => "function (attribute, value) {
                    return $('#type').value != 2;
                }"
            ],
            ['classroom_id', 'filter', 'filter' => function ($value) {
                return empty($value)?$value:Classroom::find()->where(['name'=>$value])->scalar();
            }, 'skipOnArray' => true],
            ['status', 'default', 'value' => Audit::STATUS_UNAUDITED],
            [['course_id', 'user_id', 'apply_at', 'adjust_day', 'teacher_id', 'type', 'status'], 'integer'],
            ['adjust_sec', 'filter', 'filter' => function ($value) {
                return is_array($value)?implode(',', $value):$value;
            }],
            [['apply_week', 'adjust_week', 'adjust_sec'], 'string', 'max' => 64],
            [['reason'], 'string', 'max' => 255],
            [['course_id'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['course_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['classroom_id'], 'exist', 'skipOnError' => true, 'targetClass' => Classroom::className(), 'targetAttribute' => ['classroom_id' => 'id']],
            [['teacher_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['teacher_id' => 'id']],
            [['apply_week', 'adjust_week', 'adjust_day', 'adjust_sec', 'classroom_id', 'teacher_id'], 'default', 'value' => null],

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
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'course_id' => '课程ID',
            'user_id' => '申请教师ID',
            'apply_at' => '申请时间',
            'apply_week' => '申请调整周',
            'adjust_week' => '调整周',
            'adjust_day' => '调整星期',
            'adjust_sec' => '调整节',
            'classroom_id' => '教室ID',
            'teacher_id' => '授课教师ID',
            'type' => '类型',
            'reason' => '事由',
            'status' => '状态',
        ];
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)){
            $this->apply_at = time();
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
        return $this->hasMany(Adminuser::className(), ['id' => 'adminuser_id'])
                    ->viaTable(Audit::className(), ['application_id' => 'id']);
    }
}
