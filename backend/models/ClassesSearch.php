<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Classes;

/**
 * ClassesSearch represents the model behind the search form about `common\models\Classes`.
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
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Classes::find()->where(['not',['classes.id'=>1]]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize'=>10],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // 等值连接adminuser表与classes表
        // $query->join('INNER JOIN','adminuser','classes.adminuser_id = adminuser.id');
        // $query->innerJoin('adminuser');
        // joinWith()方法默认使用左连接
        $query->joinWith('adminuser');


        // grid filtering conditions
        $query->andFilterWhere([
            // 'classes.id' => $this->id,
            'adminuser_id' => $this->adminuser_id,
        ]);

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
