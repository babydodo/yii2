<?php

namespace common\models;

use Yii;

/**
 * classroom表模型类
 *
 * @property integer $id
 * @property integer $number
 * @property string $name
 * @property integer $type
 * @property integer $amount
 *
 * @property Application[] $applications
 * @property Course[] $courses
 */
class Classroom extends \yii\db\ActiveRecord
{
    const TYPES_ORDINARY = 0;
    const TYPES_SPECIAL = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'classroom';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['number', 'name', 'type', 'amount'], 'required'],
            [['number', 'type', 'amount'], 'integer'],
            [['name'], 'string', 'max' => 128],
            [['number'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'number' => '教室代号',
            'name' => '教室名称',
            'type' => '教室类型',
            'amount' => '最多容纳班级',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApplications()
    {
        return $this->hasMany(Application::className(), ['classroom_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourses()
    {
        return $this->hasMany(Course::className(), ['classroom_id' => 'id']);
    }

    /**
     * @return string
     */
    public function getTypeStr()
    {
        return $this->type==self::TYPES_ORDINARY?'普通':'机房';
    }

    /**
     * @return array
     */
    public static function allTypes()
    {
        return [self::TYPES_ORDINARY=>'普通',self::TYPES_SPECIAL=>'机房'];
    }
}
