<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Classroom;

/**
 * 教室管理搜索过滤类
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
     * 根据过滤条件提供数据
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Classroom::find();

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

        // 表格过滤条件
        $query->andFilterWhere([
            // 'classroom.id' => $this->id,
            'type' => $this->type,
            'amount' => $this->amount,
        ]);
        $query->andFilterWhere(['like', 'number', $this->number])
            ->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
