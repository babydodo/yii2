<?php

namespace common\models;

/**
 * classes表模型类
 *
 * @property integer $id
 * @property integer $number
 * @property string $name
 * @property integer $adminuser_id
 *
 * @property Adminuser $adminuser
 * @property CourseRelationship[] $courseRelationships
 * @property User[] $users
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
            [['number', 'name'], 'unique'],
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
            'number' => '班级代号',
            'name' => '班级名称',
            'adminuser_id' => '辅导员ID',
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
    public function getCourses()
    {
        return $this->hasMany(Course::className(), ['id' => 'course_id'])
                    ->viaTable('course_relationship', ['class_id'=>'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['class_id' => 'id']);
    }

    /**
     * @return mixed
     */
    public static function allClasses()
    {
        return self::find()->select(['name','id'])->indexby('id')->column();
    }
}
