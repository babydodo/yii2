<?php

namespace common\models;

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
    const TYPE_ORDINARY = 0;    //一般类型
    const TYPE_SPECIAL = 1;     //机房或实验室类型

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'classroom';
    }

    /**
     * 属性验证规则
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['number', 'trim'],
            ['name', 'trim'],
            [['number', 'name', 'type', 'amount'], 'required'],
            [['type', 'amount'], 'integer'],
            ['type', 'in', 'range' => [self::TYPE_ORDINARY, self::TYPE_SPECIAL] ],
            [['number'], 'string', 'max' => 32],
            [['name'], 'string', 'max' => 128],
            [['number', 'name'], 'unique', 'message' => '{attribute}已存在！'],
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
            'typeStr' => '类型',
            'amount' => '容纳人数',
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
     * @return string type属性值对应的中文
     */
    public function getTypeStr()
    {
        return $this->type == self::TYPE_ORDINARY ? '普通' : '机房';
    }

    /**
     * @return array 所有教室类型
     */
    public static function allTypes()
    {
        return [
            self::TYPE_ORDINARY=>'普通',
            self::TYPE_SPECIAL=>'机房',
        ];
    }
}
