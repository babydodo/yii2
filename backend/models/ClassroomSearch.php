<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Classroom;

/**
 * Classroom模型搜索过滤类
 */
class ClassroomSearch extends Classroom
{
    /**
     * 属性验证规则
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'number', 'type', 'amount'], 'integer'],
            [['name'], 'safe'],
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
        $query = Classroom::find()->orderBy('number');  //排序

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize'=>10],   //分页
        ]);

        // 块赋值查询条件
        $this->load($params);

        // 验证不通过时返回的结果
        if (!$this->validate()) {
            return $dataProvider;
        }

        // 查询条件
        $query->andFilterWhere([
            'type' => $this->type,
            'amount' => $this->amount,
        ]);
        $query->andFilterWhere(['like', 'number', $this->number])
            ->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
