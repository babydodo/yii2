<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Course;

/**
 * 课程管理搜索过滤类
 *
 * @property mixed classroomName
 * @property mixed teacher
 */
class CourseSearch extends Course
{
    /**
     * 增加teacher,classroomName属性
     * @return array
     */
    public function attributes()
    {
        return array_merge(parent::attributes(), ['teacher','classroomName']);
    }

    /**
     * 属性验证规则
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'number', 'user_id', 'day', 'classroom_id'], 'integer'],
            [['name', 'sec', 'week', 'teacher', 'classroomName'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * 根据过滤条件提供数据
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Course::find();

        // 此处可添加初始表格限制条件

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize'=>10], //分页
        ]);

        $this->load($params);

        // 验证输入数据是否符合规则
        if (!$this->validate()) {
            return $dataProvider;
        }

        // 连接user表与classroom表
        $query->joinWith(['user', 'classroom']);

        // 表格过滤条件
        $query->andFilterWhere([
            // 'course.id' => $this->id,
            'user_id' => $this->user_id,
            'day' => $this->day,
            'classroom_id' => $this->classroom_id,
        ]);
        $query->andFilterWhere(['like', 'course.number', $this->number])
            ->andFilterWhere(['like', 'course.name', $this->name])
            ->andFilterWhere(['like', 'week', $this->week])
            ->andFilterWhere(['like', 'sec', $this->sec])
            ->andFilterWhere(['like', 'user.nickname', $this->teacher])
            ->andFilterWhere(['like', 'classroom.name', $this->classroomName]);

        // 增加teacher属性正倒排序
        $dataProvider->sort->attributes['teacher'] = [
            'asc'=>['user.nickname'=>SORT_ASC],
            'desc'=>['user.nickname'=>SORT_DESC],
        ];

        // 增加classroomName属性正倒排序
        $dataProvider->sort->attributes['classroomName'] = [
            'asc'=>['classroom.name'=>SORT_ASC],
            'desc'=>['classroom.name'=>SORT_DESC],
        ];

        return $dataProvider;
    }
}
