<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "classes".
 *
 * @property integer $id
 * @property integer $number
 * @property string $name
 * @property integer $adminuser_id
 *
 * @property Adminuser $adminuser
 * @property CourseRelationship[] $courseRelationships
 */
class Classes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'classes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['number', 'name', 'adminuser_id'], 'required'],
            [['number', 'adminuser_id'], 'integer'],
            [['name'], 'string', 'max' => 128],
            [['number'], 'unique'],
            [['adminuser_id'], 'exist', 'skipOnError' => true, 'targetClass' => Adminuser::className(), 'targetAttribute' => ['adminuser_id' => 'id']],
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
            'adminuser_id' => 'Adminuser ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdminuser()
    {
        return $this->hasOne(Adminuser::className(), ['id' => 'adminuser_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourseRelationships()
    {
        return $this->hasMany(CourseRelationship::className(), ['class_id' => 'id']);
    }
}
