<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "application".
 *
 * @property integer $id
 * @property integer $course_id
 * @property integer $user_id
 * @property integer $apply_at
 * @property integer $adjust_at
 * @property integer $classroom_id
 * @property integer $teacher_id
 * @property integer $type
 * @property string $reason
 * @property integer $status
 * @property string $remark
 *
 * @property Course $course
 * @property User $user
 * @property Classroom $classroom
 * @property User $teacher
 * @property Push[] $pushes
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
            [['course_id', 'user_id', 'apply_at', 'adjust_at', 'classroom_id', 'teacher_id', 'type', 'reason'], 'required'],
            [['course_id', 'user_id', 'apply_at', 'adjust_at', 'classroom_id', 'teacher_id', 'type', 'status'], 'integer'],
            [['reason', 'remark'], 'string', 'max' => 255],
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
            'course_id' => 'Course ID',
            'user_id' => 'User ID',
            'apply_at' => 'Apply At',
            'adjust_at' => 'Adjust At',
            'classroom_id' => 'Classroom ID',
            'teacher_id' => 'Teacher ID',
            'type' => 'Type',
            'reason' => 'Reason',
            'status' => 'Status',
            'remark' => 'Remark',
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
    public function getPushes()
    {
        return $this->hasMany(Push::className(), ['application_id' => 'id']);
    }
}
