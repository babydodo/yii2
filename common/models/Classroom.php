<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "classroom".
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
            'number' => 'Number',
            'name' => 'Name',
            'type' => 'Type',
            'amount' => 'Amount',
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
}
