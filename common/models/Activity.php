<?php

namespace common\models;

/**
 * activity表模型类
 *
 * @property integer $id
 * @property string $name
 * @property integer $adminuser_id
 * @property integer $day
 * @property string $sec
 * @property string $week
 * @property integer $classroom_id
 * @property string $classes_ids
 *
 * @property Adminuser $adminuser
 * @property Classroom $classroom
 */
class Activity extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'activity';
    }

    /**
     * 属性验证规则
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'trim'],
            [['name', 'adminuser_id', 'day', 'sec', 'week', 'classroom_id', 'classes_ids'], 'required'],
            ['classroom_id', 'filter', 'filter' => function ($value) {
                return Classroom::find()->where(['name'=>$value])->scalar();
            }, 'skipOnArray' => true],
            [['adminuser_id', 'day'], 'integer'],
            ['day', 'in', 'range' => [1, 2, 3, 4, 5, 6, 7] ],
            [['sec', 'week', 'classes_ids'],'filter', 'filter' => function ($value) {
                return is_array($value)?implode(',', $value):$value;
            }],
            [['name', 'classes_ids'], 'string', 'max' => 128],
            [['sec', 'week'], 'string', 'max' => 64],
            [['adminuser_id'], 'exist', 'skipOnError' => true, 'targetClass' => Adminuser::className(), 'targetAttribute' => ['adminuser_id' => 'id']],
            [['classroom_id'], 'exist', 'skipOnError' => true, 'targetClass' => Classroom::className(), 'targetAttribute' => ['classroom_id' => 'id']],
            // 自定义验证规则
            ['sec', 'validateTime'],
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

            // part.1 验证Course表
            $course = Course::find()
                ->andWhere(['day' => $this->day])
                ->andWhere("CONCAT(',',`sec`,',') REGEXP '[^0-9]+(".$secSelected.")[^0-9]+'")
                ->andWhere("CONCAT(',',`week`,',') REGEXP '[^0-9]+(".$weekSelected.")[^0-9]+'");

            if ($course->andWhere(['classroom_id'=>$this->classroom_id])->one()) {
                $this->addError($attribute, '教室已被占用');
            } else {

                // part.2 验证Application表
                $application = Application::find()
                    ->andWhere(['status' => Audit::STATUS_UNAUDITED])
                    ->andWhere(['adjust_day' => $this->day])
                    ->andWhere("CONCAT(',',`adjust_sec`,',') REGEXP '[^0-9]+(".$secSelected.")[^0-9]+'")
                    ->andWhere("CONCAT(',',`adjust_week`,',') REGEXP '[^0-9]+(".$weekSelected.")[^0-9]+'");

                if ($application->andWhere(['classroom_id'=>$this->classroom_id])->one()) {
                    $this->addError($attribute, '教室正在被申请使用');
                } else {

                    // part.3 验证Activity表
                    $activity = self::find()
                        ->andFilterWhere(['not', ['id'=>$this->id]])
                        ->andWhere(['day'=>$this->day])
                        ->andWhere("CONCAT(',',`sec`,',') REGEXP '[^0-9]+(".$secSelected.")[^0-9]+'")
                        ->andWhere("CONCAT(',',`week`,',') REGEXP '[^0-9]+(".$weekSelected.")[^0-9]+'");

                    if ($activity->andWhere(['classroom_id'=>$this->classroom_id])->one()) {
                        $this->addError($attribute, '教室已被占用');
                    }
                }
            }

        }
    }

    /**
     * 验证辅导员和班级在所选时间段是否空闲(验证规则)
     * @param string $attribute
     * @param array $params
     */
    public function validateTime($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $weekSelected = str_replace(',', '|', $this->week);
            $secSelected = str_replace(',', '|', $this->sec);
            $classSelected = str_replace(',', '|', $this->classes_ids);

            // part.1 验证Course表
            $course = Course::find()
                ->andWhere(['day' => $this->day])
                ->andWhere("CONCAT(',',`sec`,',') REGEXP '[^0-9]+(" . $secSelected . ")[^0-9]+'")
                ->andWhere("CONCAT(',',`week`,',') REGEXP '[^0-9]+(" . $weekSelected . ")[^0-9]+'");
            $courseCopy = clone $course;
            $classesIds = explode(',', $this->classes_ids);
            // 判断班级是否空闲
            if ($course->innerJoinWith('classes')->andWhere(['classes.id'=>$classesIds])->one()) {
                $this->addError($attribute, '所选班级在此时间段有课');
                // 判断班级是否所有人空闲
            } elseif ($courseCopy->innerJoinWith('students')->andWhere(['user.class_id'=>$classesIds])->one()) {
                $this->addError($attribute, '所选班级有学生在此时间段有课');
            } else {

                // part.2 验证Activity表
                $activity = self::find()
                    ->andFilterWhere(['not', ['id'=>$this->id]])
                    ->andWhere(['day'=>$this->day])
                    ->andWhere("CONCAT(',',`sec`,',') REGEXP '[^0-9]+(".$secSelected.")[^0-9]+'")
                    ->andWhere("CONCAT(',',`week`,',') REGEXP '[^0-9]+(".$weekSelected.")[^0-9]+'");
                $activityCopy = clone $activity;
                // 判断辅导员是否空闲
                if ($activity->andWhere(['adminuser_id'=>$this->adminuser_id])->one()) {
                    $this->addError($attribute, '您在此时间段已有活动安排');
                    // 判断班级是否有活动安排
                } elseif($activityCopy->andWhere("CONCAT(',',`classes_ids`,',') REGEXP '[^0-9]+(".$classSelected.")[^0-9]+'")->one()) {
                    $this->addError($attribute, '所选班级在此时间段已有活动安排');
                } else {

                    // part.3 验证Application表
                    $application = Application::find()->select(['course_id'])
                        ->andWhere(['status' => Audit::STATUS_UNAUDITED])
                        ->andWhere(['adjust_day' => $this->day])
                        ->andWhere("CONCAT(',',`adjust_sec`,',') REGEXP '[^0-9]+(" . $secSelected . ")[^0-9]+'")
                        ->andWhere("CONCAT(',',`adjust_week`,',') REGEXP '[^0-9]+(" . $weekSelected . ")[^0-9]+'")
                        ->column();
                    if(!empty($application) && !empty($classesIds)){
                        // 判断班级是否正被申请排课
                        if (Course::find()->innerJoinWith('classes')->andWhere(['classes.id'=>$classesIds, 'course.id'=>$application])->one()) {
                            $this->addError($attribute, '班级在此时间段正在被申请排课');
                            // 判断班级是否有学生正被申请排课
                        } elseif (Course::find()->innerJoinWith('students')->andWhere(['user.class_id'=>$classesIds, 'course.id'=>$application])->one()) {
                            $this->addError($attribute, '班级有学生在此时间段正在被申请排课');
                        }
                    }

                }
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
            'name' => '活动名称',
            'adminuser_id' => '辅导员',
            'day' => '星期',
            'sec' => '节次',
            'week' => '周次',
            'classroom_id' => '教室',
            'classes_ids' => '班级',
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
    public function getClassroom()
    {
        return $this->hasOne(Classroom::className(), ['id' => 'classroom_id']);
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

    /**
     * @return string day属性值对应的中文
     */
    public function getDayStr()
    {
        $dayStr = [
            Course::MONDAY    => '周一',
            Course::TUESDAY   => '周二',
            Course::WEDNESDAY => '周三',
            Course::THURSDAY  => '周四',
            Course::FRIDAY    => '周五',
            Course::SATURDAY  => '周六',
            Course::SUNDAY    => '周日',
        ];
        return $dayStr[$this->day];
    }
}
