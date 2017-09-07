<?php

namespace common\models;

/**
 * course_relationship表模型类
 *
 * @property integer $id
 * @property integer $class_id
 * @property integer $course_id
 *
 * @property Classes $class
 * @property Course $course
 */
class CourseRelationship extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'course_relationship';
    }

    /**
     * 属性验证规则
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['class_id', 'course_id'], 'required'],
            [['class_id', 'course_id'], 'integer'],
            [['class_id'], 'exist', 'skipOnError' => true, 'targetClass' => Classes::className(), 'targetAttribute' => ['class_id' => 'id']],
            [['course_id'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['course_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'class_id' => '班级ID',
            'course_id' => '课程ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClass()
    {
        return $this->hasOne(Classes::className(), ['id' => 'class_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourse()
    {
        return $this->hasOne(Course::className(), ['id' => 'course_id']);
    }
}
