<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;

/**
 * 用户管理搜索过滤类
 */
class UserSearch extends User
{
    /**
     * 增加className属性
     * @return array
     */
    public function attributes()
    {
        return array_merge(parent::attributes(), ['className']);
    }

    /**
     * 属性验证规则
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'class_id'], 'integer'],
            [['username', 'nickname', 'className', 'auth_key', 'password_hash', 'password_reset_token'], 'safe'],
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
        $query = User::find()->orderBy('class_id');

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

        // 连接classes表
        $query->joinWith('class');

        // 表格过滤条件
        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'nickname', $this->nickname])
            ->andFilterWhere(['like','classes.name',$this->className]);

        // 增加className属性正倒排序
        $dataProvider->sort->attributes['className'] = [
            'asc'=>['classes.name'=>SORT_ASC],
            'desc'=>['classes.name'=>SORT_DESC],
        ];

        return $dataProvider;
    }
}
