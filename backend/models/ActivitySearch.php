<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Activity;

/**
 * Activity模型搜索过滤类
 *
 * @property string classroomName
 */
class ActivitySearch extends Activity
{
    /**
     * 增加classroomName属性
     * @return array
     */
    public function attributes()
    {
        return array_merge(parent::attributes(), ['classroomName']);
    }

    /**
     * 属性验证规则
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'adminuser_id', 'day', 'classroom_id'], 'integer'],
            [['name', 'sec', 'week', 'classroomName', 'classes_ids'], 'safe'],
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
     * 根据过滤条件查询数据
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Activity::find()
            ->where(['adminuser_id'=>\Yii::$app->user->id])
            ->orderBy(['activity.id'=>SORT_DESC]); //排序

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['defaultPageSize' => 10],  //分页
        ]);

        // 块赋值查询条件
        $this->load($params);

        // 验证不通过时返回的结果
        if (!$this->validate()) {
            return $dataProvider;
        }

        // 左连接classroom表
        $query->joinWith(['classroom']);

        // 查询条件
        $query->andFilterWhere([
            'day' => $this->day,
            'classroom_id' => $this->classroom_id,
        ]);
        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'sec', $this->sec])
            ->andFilterWhere(['like', 'week', $this->week])
            ->andFilterWhere(['like', 'classes_ids', $this->classes_ids])
            ->andFilterWhere(['like', 'classroom.name', $this->classroomName]);

        // 增加classroomName属性正倒排序
        $dataProvider->sort->attributes['classroomName'] = [
            'asc'=>['classroom.name'=>SORT_ASC],
            'desc'=>['classroom.name'=>SORT_DESC],
        ];

        return $dataProvider;
    }
}
