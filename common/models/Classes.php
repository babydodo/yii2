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
 * @property Course[] $courses
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
     * 属性验证规则
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'trim'],
            [['number', 'name', 'adminuser_id'], 'required'],
            [['number', 'adminuser_id'], 'integer'],
            [['name'], 'string', 'max' => 128],
            [['number', 'name'], 'unique', 'message' => '{attribute}已存在！'],
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
            'adminuser_id' => '辅导员',
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
     * @param bool $includeTeacher
     * @return array 以id为索引的所有班级数组
     */
    public static function allClasses($includeTeacher = true)
    {
        $query = self::find()->select(['name','id'])->indexby('id');
        return $includeTeacher ? $query->column() : $query->andWhere(['not', ['id'=>User::TEACHER_CLASS]])->column();
    }

    /**
     * @param $adminuserID
     * @return array|null 对应辅导员管理的班级
     */
    public static function adminuserClasses($adminuserID = null)
    {
        return empty($adminuserID) ? null :
            self::find()->select(['name','id'])
                ->where(['adminuser_id'=>$adminuserID])
                ->indexby('id')
                ->column();
    }
}
