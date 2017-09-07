<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Classes;

/**
 * Classes模型搜索过滤类
 *
 * @property string counselor
 */
class ClassesSearch extends Classes
{
    /**
     * 增加counselor属性
     * @return array
     */
    public function attributes()
    {
        return array_merge(parent::attributes(), ['counselor']);
    }

    /**
     * 属性验证规则
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'number', 'adminuser_id'], 'integer'],
            [['name', 'counselor'], 'safe'],
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
        $query = Classes::find()
            ->where(['not',['classes.id'=>1]])  //排除教师班
            ->orderBy('number');  //排序

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

        // 左连接adminuser表
        $query->joinWith('adminuser');

        // 查询条件
        $query->andFilterWhere(['like', 'number', $this->number])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'adminuser.nickname', $this->getAttribute('counselor')]);

        // 增加counselor属性正倒排序
        $dataProvider->sort->attributes['counselor'] = [
            'asc'=>['adminuser.nickname'=>SORT_ASC],
            'desc'=>['adminuser.nickname'=>SORT_DESC],
        ];

        return $dataProvider;
    }
}
