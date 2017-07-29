<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Adminuser;

/**
 * 管理员管理搜索过滤类
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
     * 根据过滤条件提供数据
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Adminuser::find()->orderBy('role');

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
        $query->andFilterWhere(['role' => $this->role]);
        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'nickname', $this->nickname])
            ->andFilterWhere(['like', 'email', $this->email]);

        return $dataProvider;
    }
}
