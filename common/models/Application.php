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
            [['course_id', 'user_id', 'apply_at', 'apply_week', 'adjust_week', 'adjust_day', 'adjust_sec', 'classroom_id', 'teacher_id', 'type', 'reason'], 'required'],
            [['course_id', 'user_id', 'apply_at', 'adjust_day', 'classroom_id', 'teacher_id', 'type', 'status'], 'integer'],
            [['apply_week', 'adjust_week', 'adjust_sec'], 'string', 'max' => 64],
            [['reason'], 'string', 'max' => 255],
            [['course_id'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['course_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['classroom_id'], 'exist', 'skipOnError' => true, 'targetClass' => Classroom::className(), 'targetAttribute' => ['classroom_id' => 'id']],
            [['teacher_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['teacher_id' => 'id']],
        ];
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
