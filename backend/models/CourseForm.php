<?php

namespace backend\models;

use common\models\Course;
use common\models\CourseRelationship;

/**
 * 课程新增与修改表单模型类
 * @property array classID
 */
class CourseForm extends Course
{
    /**
     * 增加classID属性
     * @return array
     */
    public function attributes()
    {
        return array_merge(parent::attributes(), ['classID']);
    }

    /**
     * 属性验证规则
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(),[
            ['classID', 'safe'],
            [['sec'], 'validateTime'],
            [['day', 'week'], 'validateTime'],
        ]);
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), ['classID'=>'班级']);
    }

    /**
     * 验证教师和班级在所选时间段是否空闲(验证规则)
     * @param string $attribute
     * @param array $params
     */
    public function validateTime($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $secSelected = str_replace(',', '|', $this->sec);
            $weekSelected = str_replace(',', '|', $this->week);

            // 筛选选定时间段所有信息
            $query = Course::find();
            $query->andFilterWhere(['not', ['course.id'=>$this->id]]);
            $query->andWhere(['day' => $this->day]);
            $query->andWhere("CONCAT(',',`sec`,',') REGEXP '[^0-9]+(" . $secSelected . ")[^0-9]+'");
            $query->andWhere("CONCAT(',',`week`,',') REGEXP '[^0-9]+(" . $weekSelected . ")[^0-9]+'");

            $queryCopy1 = clone $query;
            $queryCopy2 = clone $query;

            // 判断老师是否空闲
            if ($query->andWhere(['user_id' => $this->user_id])->one()) {
                $this->addError($attribute, '该教师在此时间段有课');
            } elseif(!empty($this->classID)) {
                // 判断班级是否空闲
                if ($queryCopy1->innerJoinWith('classes')->andFilterWhere(['classes.id'=>$this->classID])->one()) {
                    $this->addError($attribute, '所选班级在此时间段有课');
                    // 判断班级是否所有人空闲
                } elseif ($queryCopy2->innerJoinWith('students')->andFilterWhere(['user.class_id'=>$this->classID])->one()) {
                    $this->addError($attribute, '所选班级有学生在此时间段有课');
                }
            }
        }
    }

    /**
     * 保存课程及班级关联信息
     * @param null $id
     * @return bool
     */
    public function saveCourse($id = null)
    {
        // 验证输入数据
        if (!$this->validate()) {
            return null;
        }

        // 判断更新还是新增
        if ($id){
            $course = Course::findOne($id);
        } else {
            $course = new Course();
        }
        // course模型属性赋值
        $course->number = $this->number;
        $course->name = $this->name;
        $course->user_id = $this->user_id;
        $course->day = $this->day;
        $course->sec = $this->sec;
        $course->week = $this->week;
        $course->classroom_id = $this->classroom_id;

        if ($course->save(false) ? true : false) {
            // 根据course的id删除course_relationship表建立的关系
            $course_id = $course->getAttribute('id');
            CourseRelationship::deleteAll(['course_id'=>$course_id]);
            // 根据前台传入的班级向course_relationship表写入关联数据
            if (!empty($this->classID)) {
                foreach ($this->classID as $class_id) {
                    $relation = new CourseRelationship();
                    $relation->class_id = $class_id;
                    $relation->course_id = $course_id;
                    $relation->save();
                }
            }
            return true;
        } else{
            return false;
        }
    }
}
