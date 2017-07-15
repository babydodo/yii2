<?php

namespace common\models;

use function GuzzleHttp\Psr7\_caseless_remove;
use Yii;

/**
 * course表模型类
 *
 * @property integer $id
 * @property integer $number
 * @property string $name
 * @property integer $user_id
 * @property integer $day
 * @property string $sec
 * @property string $week
 * @property integer $classroom_id
 *
 * @property Application[] $applications
 * @property User $user
 * @property Classroom $classroom
 * @property CourseRelationship[] $courseRelationships
 * @property Elective[] $electives
 */
class Course extends \yii\db\ActiveRecord
{
    const MONDAY = 1;
    const TUESDAY = 2;
    const WEDNESDAY = 3;
    const THURSDAY = 4;
    const FRIDAY = 5;
    const SATURDAY = 6;
    const SUNDAY = 7;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'course';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['number', 'name', 'user_id', 'day', 'sec', 'week', 'classroom_id'], 'required'],
            [['number', 'user_id', 'day', 'classroom_id'], 'integer'],
            [['name'], 'string', 'max' => 128],
            [['sec', 'week'], 'string', 'max' => 64],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['classroom_id'], 'exist', 'skipOnError' => true, 'targetClass' => Classroom::className(), 'targetAttribute' => ['classroom_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'number' => '课程代号',
            'name' => '课程名',
            'user_id' => '教师ID',
            'day' => '星期',
            'sec' => '节',
            'week' => '授课周',
            'classroom_id' => '教室ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApplications()
    {
        return $this->hasMany(Application::className(), ['course_id' => 'id']);
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
    public function getCourseRelationships()
    {
        return $this->hasMany(CourseRelationship::className(), ['course_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getElectives()
    {
        return $this->hasMany(Elective::className(), ['course_id' => 'id']);
    }

    /**
     * @return string
     */
    public function getDayStr()
    {
        switch ($this->day) {
            case self::MONDAY : $dayStr = '周一'; break;
            case self::TUESDAY : $dayStr = '周二'; break;
            case self::WEDNESDAY : $dayStr = '周三'; break;
            case self::THURSDAY : $dayStr = '周四'; break;
            case self::FRIDAY : $dayStr = '周五'; break;
            case self::SATURDAY : $dayStr = '周六'; break;
            case self::SUNDAY : $dayStr = '周日'; break;
            default : $dayStr = NULL;
        }
        return $dayStr;
    }

    /**
     * @return array
     */
    public static function allDays()
    {
        return [
            self::MONDAY => '周一',
            self::TUESDAY => '周二',
            self::WEDNESDAY => '周三',
            self::THURSDAY => '周四',
            self::FRIDAY => '周五',
            self::SATURDAY => '周六',
            self::SUNDAY => '周日',
        ];
    }
}
