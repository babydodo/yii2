<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Adminuser;

/**
 * Adminuser模型搜索过滤类
 */
class AdminuserSearch extends Adminuser
{
    /**
     * 属性验证规则
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'role'], 'integer'],
            [['username', 'nickname', 'email'], 'safe'],
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
        $query = Adminuser::find()->orderBy('role'); //排序

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize'=>10], //分页
        ]);

        // 块赋值查询条件
        $this->load($params);

        // 验证不通过时返回的结果
        if (!$this->validate()) {
            return $dataProvider;
        }

        // 查询条件
        $query->andFilterWhere(['role' => $this->role]);
        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'nickname', $this->nickname])
            ->andFilterWhere(['like', 'email', $this->email]);

        return $dataProvider;
    }
}
