<?php

namespace common\models;

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
 * @property Classes[] $classes
 * @property Elective[] $students
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
     * 属性验证规则
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'trim'],
            [['number', 'name', 'user_id', 'day', 'sec', 'week', 'classroom_id'], 'required'],
            ['classroom_id', 'filter', 'filter' => function ($value) {
                return Classroom::find()->where(['name'=>$value])->scalar();
            }, 'skipOnArray' => true],
            [['number', 'user_id', 'day'], 'integer'],
            ['day', 'in', 'range' => [1, 2, 3, 4, 5, 6, 7] ],
            [['name'], 'string', 'max' => 128],
            [['sec', 'week'],'filter', 'filter' => function ($value) {
                return is_array($value)?implode(',', $value):$value;
            }],
            [['sec', 'week'], 'string', 'max' => 64],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            ['classroom_id', 'exist', 'skipOnError' => true, 'targetClass' => Classroom::className(), 'targetAttribute' => ['classroom_id' => 'id']],
            // 自定义规则
            ['classroom_id', 'validateClassroom'],
        ];
    }

    /**
     * 验证教室在所选时间段是否空闲(验证规则)
     * @param string $attribute
     * @param array $params
     */
    public function validateClassroom($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $secSelected = str_replace(',', '|', $this->sec);
            $weekSelected = str_replace(',', '|', $this->week);

            $query = self::find();
            $query->andFilterWhere(['not', ['id'=>$this->id]]);
            $query->andWhere(['day'=>$this->day]);
            $query->andWhere("CONCAT(',',`sec`,',') REGEXP '[^0-9]+(".$secSelected.")[^0-9]+'");
            $query->andWhere("CONCAT(',',`week`,',') REGEXP '[^0-9]+(".$weekSelected.")[^0-9]+'");

            if ($query->andWhere(['classroom_id'=>$this->classroom_id])->one()) {
                $this->addError($attribute, '教室已被占用');
            }
        }
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
            'user_id' => '教师',
            'teacher' => '教师',
            'day' => '星期',
            'sec' => '节',
            'week' => '授课周',
            'classroom_id' => '教室',
            'classroomName' => '教室',
            'classroom_number' => '教室代号',
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
    public function getClasses()
    {
        return $this->hasMany(Classes::className(), ['id' => 'class_id'])
                    ->viaTable('course_relationship', ['course_id'=>'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudents()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id'])
                    ->viaTable('elective', ['course_id'=>'id']);
    }

    /**
     * @return string day属性值对应的中文
     */
    public function getDayStr()
    {
        $dayStr = [
            self::MONDAY    => '周一',
            self::TUESDAY   => '周二',
            self::WEDNESDAY => '周三',
            self::THURSDAY  => '周四',
            self::FRIDAY    => '周五',
            self::SATURDAY  => '周六',
            self::SUNDAY    => '周日',
        ];
        return $dayStr[$this->day];
    }

    /**
     * @return array 周一至周日数组
     */
    public static function allDays()
    {
        return [
            self::MONDAY    => '周一',
            self::TUESDAY   => '周二',
            self::WEDNESDAY => '周三',
            self::THURSDAY  => '周四',
            self::FRIDAY    => '周五',
            self::SATURDAY  => '周六',
            self::SUNDAY    => '周日',
        ];
    }

    /**
     * @return array 1-12节数组
     */
    public static function allSections()
    {
        $secList = array();
        for ($i=1;$i<=12;$i++) {
            $secList[$i]=$i;
        }
        return $secList;
    }

    /**
     * @return array 1-16周数组
     */
    public static function allWeeks()
    {
        $weekList = array();
        for ($i=1;$i<=16;$i++) {
            $weekList[$i]=$i;
        }
        return $weekList;
    }

    /**
     * 格式化sec与week属性.
     * 如果属性是字符串则转化为数组.
     */
    public function formatAttributes()
    {
        $this->sec = is_string($this->sec) ? explode(',', $this->sec): $this->sec;
        $this->week = is_string($this->week) ? explode(',', $this->week) : $this->week;
        return ;
    }
}
